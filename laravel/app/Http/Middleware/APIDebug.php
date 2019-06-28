<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;

class APIDebug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->log($request);
        return $next($request);
    }

    private function log($request){
        $content = $this->content($request);
        $this->store($content);
    }

    private function log2($response){
        $content = $this->content2($response);
        $this->store($content);
    }

    private function store($content){
        Storage::disk('debug')->append('api.log',$content);
    }

    private function content($request){
        $lines = []; $lines[] = self::$separator;
        $lines[] = self::colon_separate(['Date Time',date('D d/M/Y - h:i:s A')]);
        $lines[] = self::colon_separate(['IP',$request->ip()]); $lines[] = self::colon_separate(['UserAgent',$request->userAgent()]);
        $lines[] = self::colon_separate(['Full URL',$request->fullUrl()]); $lines[] = self::colon_separate(['Method',$request->method()]);
        $lines[] = self::heading('HTTP Request Headers'); $lines[] = self::name_value($request->headers->all());
        $lines[] = self::heading('Query'); $lines[] = self::name_value($request->toArray());
        return implode("\n",$lines);
    }

    private function content2($response){
        $lines = [];
        $lines[] = self::heading('Response'); $lines[] = $response->content();
        $lines[] = self::$separator; $lines[] = "";
        return implode("\n",$lines);
    }

    static private $separator = "==================================================================================";
    static private $divider = "----------------------------------------------";
    static private function heading($text){ return implode("\n",["",str_pad("",strlen($text),"-"),$text,str_pad("",strlen($text),"-")]); }
    static private function array_separate($array,$glue){ return implode($glue,$array); }
    static private function pipe_separate($array){ return self::array_separate($array," | "); }
    static private function colon_separate($array){ return self::array_separate($array,": "); }
    static private function arrow_separate($array){ return self::array_separate($array," -> "); }
    static private function coma_separate($array){ return self::array_separate($array,", "); }
    static private function name_value($array){
        if(empty($array)) return "";
        $clean = [];
        foreach ($array as $name => $value)
            $clean[] = self::arrow_separate([$name,is_array($value) ? self::coma_separate($value) : $value]);
        return implode("\n",$clean);
    }

    static private function get_time(){
        return ;
    }

    public function terminate($request, $response)
    {
        $this->log2($response);
    }
}
