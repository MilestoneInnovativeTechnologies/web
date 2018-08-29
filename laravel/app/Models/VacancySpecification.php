<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacancySpecification extends Model
{
    protected $table = 'vacancy_specs';
    //protected $primaryKey = 'code';
    //public $incrementing = false;
    public $timestamps = true;
    //protected $fillable = ['code','title','description'];
    protected $guarded = [];
    //protected $hidden = ['created_at','updated_at'];
    //protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
    //protected $with = ['Customer','User'];

    public function vacancy(){
        return $this->belongsTo('App\Models\Vacancy','vacancy','code');
    }
}
