<?php

namespace App\Model;

use App\Model\Trait\HasImage;
use App\Model\Trait\IsActive;
use App\Model\Trait\PaginateSortAndFilter;
use \Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {

    use Notifiable, Authenticatable, Authorizable, CanResetPassword;
    use HasImage;
    use IsActive;
    use PaginateSortAndFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username' ,'email', 'password', 'phone', 'role_id', 'api_token' , 'active',
    ];

    public static $rules = [
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:6'
    ];

    protected $filterableFields  = ['name', 'username', 'email'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'api_token' , 'remember_token',
    ];


    protected $defaultImage = "resources/images/icons/profile.png";
    

    public static function findBySlug(string $slug){

        $user = self::where('slug',$slug)->first();

        if(!empty($user)){
            return $user;
        }else{

            foreach (self::where('slug',null)->orWhere('slug',"")->get() as $user) {
                if(str_slug($user->username)==$slug){
                    return $user;
                }
            }

        }

        return null;
    }


    public function blogposts(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(\App\Model\Blogpost::class,'author_id','id');
    }


    /**
    *
    * If the binded UserRole is missing then ithe method
    * return the default role. 
    *
    */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo {

        if(\App\Model\UserRole::find($this->role_id)==null){
            $this->role_id = 1;
        }
         
        return $this->belongsTo(\App\Model\UserRole::class,'role_id','id');
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany {
    	return $this->hasMany(\App\Model\BlogpostComment::class,'author_id','id');
    }


    public function hasPermission(string $permission): bool {

        return empty($this->role->rights)? false : in_array($permission, $this->role->rights);
    }

    /**
    *
    * Checking that the user role which the user assegned to,
    * has the right to enter into the admin area. Therefore
    * the user is an admingroup (default: editor, manager, admin) user. 
    * Don't mix up with Admin as a role! 
    *
    */
    public function isAdmin(): bool {
        return $this->hasPermission('admin_area');
    }

    /**
    *
    * https://erikbelusic.com/tracking-if-a-user-is-online-in-laravel/
    *
    */
    public function isOnline(): bool {

    	return Cache::has('user-is-online-' . $this->id);
	}

    public function getSlug(): string {
        return empty($this->slug)? str_slug($this->username) : $this->slug;
    }

    /**
    * Mutator for passwords
    */
    public function setPasswordAttribute(string $value): void {
    	$this->attributes['password'] = \Hash::make($value);
    }

    public function scopeEmail(Builder $query, string $email): Builder {
        return $query->where('email', $email);
    }

}
