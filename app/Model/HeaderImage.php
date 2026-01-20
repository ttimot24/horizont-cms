<?php

namespace App\Model;

use \Illuminate\Database\Eloquent\Model;
use App\Model\Trait\HasAuthor;
use App\Model\Trait\HasImage;
use App\Model\Trait\IsActive;

class HeaderImage extends Model {

    use HasImage;
    use HasAuthor;
    use IsActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'type' ,'link' ,'description', 'image', 'active',
    ];

    protected $defaultImage = '';

}
