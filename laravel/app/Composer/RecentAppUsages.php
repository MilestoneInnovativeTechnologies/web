<?php

    namespace App\Composer;

    use App\Models\CustomerRegistration;
    use Illuminate\View\View;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Http\Request;

    class RecentAppUsages
    {
        private $code = null;
        private $log = null;
        private $fetch_deep = 4;
        private $days = 5;
        private $seq_cache = [];

        public function __construct(Request $request)
        {
            $this->code = $request->code;
            $this->fetchLog();
            $this->filterLog();
        }

        private function fetchLog(){
            $log = [];
            do{
                $file = \App\Http\Controllers\AppInitController::getCustomerFile($this->fetch_deep);
                $array_contents = (array) $this->getFileContents($file);
                $log = array_merge($log,$array_contents);
            } while($this->fetch_deep--);
            $this->log = $log;
        }

        private function getFileContents($file){
            $content = Storage::exists($file) ? Storage::get($file) : "";
            return array_filter(preg_split("/\r\n|\n|\r/", $content),function($line){ return trim($line) !== ''; });
        }

        public function compose(View $view){
            $view->with('log',$this->log);
        }

        private function filterLog(){
            $code = $this->code;
            $this->log = collect($this->log)
                ->map(function($line){ return $this->spreadLine($line); })
                ->reverse()
                ->filter(function($item)use($code){ return $item['customer'] === $code; })
                ->groupBy('date')->take($this->days)
                ->map(function($item){ return $item->groupBy('product')->map(function($item){ return $item->groupBy('version'); }); })
                ->toArray();
        }

        private function spreadLine($line){
            $lines = explode("\t",$line);
            list($day,$date,$time,$customer,$seq,$p_code,$e_code,$version,$key) = $lines;
            $time = date("g:i a",strtotime($time));
            $date = $day . " " . $date;
            $product = $this->getProduct($customer,$seq);
            return compact('date','time','customer','product','version','key');
        }

        private function getProduct($customer,$seq){
            if(!array_key_exists($seq,$this->seq_cache)) $this->addSeqCache($customer);
            return array_get($this->seq_cache,$seq);
        }

        private function addSeqCache($customer){
            $cr = CustomerRegistration::where(compact('customer'))->with(['Product','Edition'])->get();
            $this->seq_cache = $cr->mapWithKeys(function($item){
                $value = implode(" ",[$item->Product->name,$item->Edition->name,'Edition',$item->remarks?"({$item->remarks})":'']);
                return [$item->seqno => $value];
            })->toArray();
        }

    }