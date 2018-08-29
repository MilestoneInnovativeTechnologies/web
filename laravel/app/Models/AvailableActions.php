<?php
/**
 * Created by PhpStorm.
 * User: Firose Hussain
 * Date: 24-07-2018
 * Time: 10:51 AM
 */

namespace App\Models;


trait AvailableActions
{
//    public $actions = ['view','update','delete'];
//    public $conditional_action = [];
//    public $role_groups = [['support_team'],['company']];
//    public $group_actions = [0=>[0],1=>[1],2=>[2]];
//    public $default_group = 0;
//    public $modal_actions = ['create'];
//
//    public $action_title = ['view' => 'View', 'update' => 'Update', 'delete' => 'Delete'];
//    public $action_icon = ['view' => 'view', 'update' => 'update', 'delete' => 'delete'];


    protected function _GETROLEGROUP($rolename){ foreach($this->role_groups as $grp => $names) if(in_array($rolename,$names)) return $grp; return $this->default_group; }
    protected function _GETGROUPACTIONS($group){ return $this->group_actions[$group]; }
    protected function _GETROLEACTIONS($role){ return $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
    public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
    public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }

    //protected $appends = ['available_actions'];
    public function getAvailableActionsAttribute($value = null){
        $AuthUser = $this->_GETAUTHUSER(); if(!$AuthUser) return [];
        $role = $this->_GETAUTHUSER()->rolename;
        $role_actions = $this->_GETROLEACTIONS($role);
        if(!$this->exists) return $this->_GETARRAYVALUES($this->actions,$role_actions);
        $actions = array_filter($role_actions,function($ra){ return ($this->conditional_action && array_key_exists($ra,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ra]],$this) : true; });
        return $this->_GETARRAYVALUES($this->actions,$actions);
    }
}