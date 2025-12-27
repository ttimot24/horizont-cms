<?php

namespace App\Model;

use \Illuminate\Database\Eloquent\Model;

class Visits extends Model {
    
    protected $table = 'visits';

    public $timestamps = false;

    public static function newVisitor(\Illuminate\Http\Request $request): ?Visits {
    	$visit = null;

    	try{

    		$visit = new Visits();
    		$visit->date = date('Y-m-d'); //TODO Use created_at instead
    		$visit->time = date('H:i:s');
    		$visit->ip = $request->ip();
    		$visit->host_name = gethostbyaddr($request->ip());
    		$visit->client_browser = $request->header('user_agent');

    		$visit->save();

    	}catch(\Exception $exception){
          //  if($request->input('debug')=='true'){ throw $exception; }
    		//do nothing
    	}

    	return $visit;
    }


	public function getCreatedAtAttribute(): \Carbon\Carbon {	
        return new \Carbon\Carbon($this->date." ".$this->time);
    }

}
