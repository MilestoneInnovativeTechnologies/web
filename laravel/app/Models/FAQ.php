<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use AvailableActions;

    protected $table = 'faqs';
    protected $guarded = [];
    protected $with = ['Scope','Products','Categories'];

    protected $appends = ['tags','available_actions'];//
    protected function getTagsAttribute(){
        $PTgs = ($this->Products && $this->Products->isNotEmpty()) ? $this->Products->map(function($Rec){ return implode(" ",[($Rec->Product) ? $Rec->Product->name : '',($Rec->Edition) ? $Rec->Edition->name : '']); })->toArray() : [];
        $CTgs = $this->Categories ? $this->Categories->categories : [];
        return array_unique(array_merge($PTgs, $CTgs));
    }

    public $actions = ['view','edit','scope','product','delete','undelete','category'];
    public $conditional_action = [4 => 'isNotDeleted', 5 => 'isDeleted'];
    public $role_groups = [[],['supportteam','supportagent','company']];
    public $group_actions = [0=>[],1=>[0,1,2,3,4,5,6]];
    public $default_group = 0;
    public $modal_actions = ['create'];

    public $action_title = ['view' => 'View details', 'edit' => 'Edit details', 'scope' => 'Manage scopes', 'product' => 'Manage products', 'delete' => 'Make Inactive', 'undelete' => 'Make active', 'undelete' => 'Make active', 'category' => 'Manage Categories'];
    public $action_icon = ['view' => 'list-alt', 'edit' => 'pencil', 'scope' => 'fullscreen', 'product' => 'tags', 'delete' => 'remove', 'undelete' => 'ok', 'category' => 'tasks'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where(function($Q){ $Q->where('status','ACTIVE'); })->orWhere(function($Q){
                $Q->where('status','INACTIVE')->where('updated_at','>',date('Y-m-d H:i:s',strtotime('-48 hours')));
            });
        });
    }

    public function isDeleted($data){
        return !$this->isNotDeleted($data);
    }

    public function isNotDeleted($data){
        return $data->status === 'ACTIVE';
    }

    public function scopePublic($Q){
        return $Q->whereHas('Scope',function($Q){ $Q->where('public','YES'); });
    }

    public function scopeActive($Q){
        return $Q->where('status','ACTIVE');
    }

    public function scopeSearch($Q){
        $st = request()->search_text;
        if($st){
            $like = '%'.$st.'%';
            return $Q->where('question','like',$like)->orWhere('answer','like',$like);
        }
    }

    public function scopeMy($Q){
        $Partner = $this->_GETAUTHUSER()->partner;
        return $Q->whereHas('Scope',function($Q)use($Partner){ $Q->where('partner',$Partner); });
    }

    protected $role2filter = ['distributor' => 'dealer', 'support' => ['supportteam','supportagent'], 'ignore' => ['company','webdeveloper','scm']];
    protected function getRoleFilter($role){
        foreach($this->role2filter as $filter => $roles){
            if(is_array($roles)){
                if(in_array($role,$roles)) return $filter;
            } elseif($role == $roles) return $filter;
        }
        return $role;
    }

    public function scopeMyRole($Q){
        $rolename = $this->_GETAUTHUSER()->rolename;
        $filter = $this->getRoleFilter($rolename); if($filter == 'ignore') return $Q;
        return $Q->whereHas('Scope',function($Q)use($filter){ $Q->where($filter,'YES'); });
    }

    protected function getProducts(){
        $Qry = $this->_GETAUTHUSER()->rolename == 'customer' ? ['CustomerRegistration','customer'] : ['PartnerProduct','partner'];
        $Cls = "\\App\\Models\\" . $Qry[0]; return $Cls::where($Qry[1],$this->_GETAUTHUSER()->partner)->get();
    }

    public function scopeMyProduct($Q){
        $Products = $this->getProducts();
        return $Q->public()->whereHas('Products',function ($Q) use ($Products) {
            foreach($Products as $K => $Record) {
                $Qry = ($K) ? 'orWhere' : 'where';
                $Q->$Qry(function ($Q) use ($Record) {
                    $Q->where('product', $Record->product)->where(function ($Q) use ($Record) {
                        $Q->where('edition', $Record->edition)->orWhereNull('edition');
                    });
                });
            }
        });
    }

    public function Scope(){
        return $this->hasOne('App\Models\FAQScope','question','id');
    }

    public function Products(){
        return $this->hasMany('App\Models\FAQProduct','question','id');
    }

    public function Categories(){
        return $this->hasOne('App\Models\FAQCategory','question','id');
    }

    public function Creator(){
        return $this->belongsTo('App\Models\Partner','created_by','code');
    }
}
