@php
    function parentFeatures(){
        return \App\Models\SK\Feature::with('Children.Children.Children')->whereStatus('Active')->whereNull('parent')->get()->flatMap(function($feature){
            return FeatureOption($feature,'');
        })->prepend(['text' => 'None', 'value' => null, 'attr' => 'level="-1"'])->toArray();
    }
    function FeatureOption($feature,$parent){
        $opts = []; $name = $feature->name;
        $attr = 'level="' . $feature->level . '"'; $value = $feature->id;
        $text = ($parent ? ($parent . ' > ') : '') . $name;
        $opts[] = compact('text','value','attr');
        if($feature->Children && $feature->Children->isNotEmpty()){
            $feature->Children->each(function($feature)use(&$opts,$name){ foreach(FeatureOption($feature,$name) as $opt) $opts[] = $opt; });
        }
        return $opts;
    }
@endphp