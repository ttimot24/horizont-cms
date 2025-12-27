<?php

namespace App\Model;

use \Illuminate\Database\Eloquent\Model;

class UserRole extends Model {
   
	public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'rights'
    ];

	
	public function users(){
		return $this->hasMany(\App\Model\User::class,'role_id','id');
	}

	/**
    * Accessor for rights
    */
    public function getRightsAttribute(){
        return json_decode($this->attributes['rights'], true) ?? [];
    }

	/**
    * Mutator for rights
    */
    public function setRightsAttribute($value): void{
    	$this->attributes['rights'] = json_encode($value);
    }

    public function addRight($right){
        $all_rights = $this->getRightsAttribute();
        array_push($all_rights, $right);
        $this->setRightsAttribute($all_rights);
    }

    public function isAdminRole(): bool {
        $roles = $this->getRightsAttribute();

        return $roles? in_array('admin_area',$roles) : false;
    }

}
