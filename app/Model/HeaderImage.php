<?php

namespace App\Model;

use App\Model\Trait\HasAuthor;
use App\Model\Trait\HasImage;
use App\Model\Trait\IsActive;
use Illuminate\Database\Eloquent\Model;

class HeaderImage extends Model
{
    use HasAuthor;
    use HasImage;
    use IsActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'type', 'link', 'description', 'image', 'active',
    ];

    protected $defaultImage = '';
}
