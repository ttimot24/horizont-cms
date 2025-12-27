<?php

namespace App\Model;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Builder;

class Settings extends Model {
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting', 'value', 'more'
    ];

  	protected $table = 'settings';
  	public $settings;
  	private static $static_settings = null;

	public static function get(string $setting, $default = null, bool $valueCheck = false){

		if(!$valueCheck && self::has($setting)){
			return self::$static_settings[$setting];
		}

		if($valueCheck && self::has($setting) && !empty(self::$static_settings[$setting])){
			return self::$static_settings[$setting];
		}
		
		return $default;
	}

	public static function has(string $setting): bool {
		if(self::$static_settings==null){
			self::$static_settings = self::getAll();	
		}

		return array_key_exists($setting, self::$static_settings);
	}


	public static function getAll(): array {

		$array = [];

		foreach(self::all() as $each){
			$array[$each->setting] = $each->value;
		}

		return $array;
	}


	public function assignAll(): void {
		$settings = self::all();

		$this->settings = new \stdClass();

		foreach($settings as $each){

			if(!empty($each->setting)){
				$this->settings->{$each->setting} = $each->value;
			}
		
		}
	}

	public function scopeGroup(Builder $query, string $group): Builder {
		return $query->where('group', $group);
	}

}
