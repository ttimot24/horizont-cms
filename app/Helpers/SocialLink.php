<?php

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Collection;

class SocialLink extends Model {
  
  	protected $table = 'settings';
  	public $timestamps = false;


  	public function getName(): string {
  		return str_replace("social_link_","",$this->setting);
  	}

	public static function all($columns = array()): Collection {
		return self::where('setting','LIKE','social_link_%')->get();
	}

	public static function getLinkTo(string $social_media): string {

		$media = self::where('setting','LIKE','social_link_'.strtolower($social_media))->first();
		return $media==null? "" : $media->value;
	}


}
