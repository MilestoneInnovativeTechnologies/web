<?php

namespace App\Models\SK;

use App\Models\AvailableActions;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use AvailableActions;

    protected $table = 'sk_clients';
    protected $guarded = [];
    protected static function boot(){
        parent::boot();
        static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where(function($Q){ $Q->where('status','Active'); });
        });
    }

    public $actions = ['detail'];
    public $conditional_action = [];
    public $role_groups = [['company'],[]];
    public $group_actions = [0=>[0]];
    public $default_group = 0;
    public $modal_actions = ['add'];
    public $action_title = ['detail' => 'View Details'];
    public $action_icon = ['detail' => 'fullscreen'];

    public function Partner(){ return $this->belongsTo(Partner::class,'partner','code'); }
    public function Branches(){ return $this->hasMany(Branch::class,'client','id'); }
}
