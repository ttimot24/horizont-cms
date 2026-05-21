<?php

namespace App\Model;

use App\Model\Trait\HasAuthor;
use App\Model\Trait\IsActive;
use Illuminate\Database\Eloquent\Model;

class BlogpostComment extends Model
{
    use HasAuthor;
    use IsActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'blogpost_id', 'author_id', 'comment', 'active',
    ];

    public function blogpost()
    {
        return $this->belongsTo(\App\Model\Blogpost::class, 'blogpost_id', 'id');
    }

    public function user()
    {
        return $this->author();
    }
}
