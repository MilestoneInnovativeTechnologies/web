<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicPrintObjectSpecs extends Model
{

    public $specs = [
        'spec0' =>  ['Size',['A4','A5','8CM Roll Paper']],
        'spec1' =>  ['Orientation',['Portrait','Landscape']],
        'spec2' =>  ['Tax',['NA','VAT','GST']],
        'spec3' =>  ['Country',['Any','India','UAE','Bahrain','GCC']],
        'spec4' =>  ['Discount',['No Discount','Before','After','Both']],
    ];

    protected $guarded = [];
    protected $hidden = ['spec0','spec1','spec2','spec3','spec4','spec5','spec6','spec7','spec8','spec9'];


    protected $appends = ['details'];
    public function getDetailsAttribute($value = null){
        $specs = [];
        foreach($this->specs as $DBName => $NameOptionsArray){
            if($this->{$DBName} && $this->{$DBName} !== 'NA')
                $specs[$NameOptionsArray[0]] = $this->{$DBName};
        }
        return $specs;
    }

    public function printObject(){
        $this->belongsTo('App\Models\PublicPrintObject','print_object','code');
    }


}
