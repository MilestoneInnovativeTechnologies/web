<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorExcludeCategory extends Model
{

	protected $table = 'distributor_exclude_categories';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_by','created_at','updated_at'];
	//protected $visible = [];
	//protected $with = [];
	protected static function boot() {
		parent::boot();
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->has('Distributor');
		});
	}
	
	public function distributor(){ return $this->belongsTo('App\Models\Distributor','distributor','code')->select('code','name'); }
	public function category(){ return $this->belongsTo('App\Models\TicketCategoryMaster','category','code'); }
	public function createdBy(){ return $this->belongsTo('App\Models\Partner','created_by','code'); }
	
	public function create_new($distributor, $categories){
		$this->delete_categories($distributor);
		if(!empty($categories)){
			if(is_array($categories)) foreach($categories as $category) $this->add_new($distributor,$category,Auth()->user()->partner);
			else $this->add_new($distributor,$categories,Auth()->user()->partner);
		}
	}
	
	public function delete_categories($distributor){
		$this->whereDistributor($distributor)->delete();
	}
	
	public function add_new($distributor,$category,$created_by){
		return $this->create(compact('distributor','category','created_by'));
	}
	
	public function get_categories($distributor){
		return $this->withoutGlobalScope('own')->whereDistributor($distributor)->pluck('category')->toArray();
	}
	
}
