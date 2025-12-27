<?php

namespace App\Model;

use \Illuminate\Database\Eloquent\Model;
use \App\Model\Trait\HasAuthor;
use \App\Model\Trait\Draftable;
use App\Model\Trait\HasImage;
use App\Model\Trait\IsActive;
use App\Model\Trait\PaginateSortAndFilter;

class Blogpost extends Model
{

    use HasImage;
    use HasAuthor;
    use Draftable;
    use PaginateSortAndFilter;
    use IsActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title','slug', 'summary', 'text', 'language', 'author_id', 'comments_enabled', 'active',
    ];

    protected $appends = ['category_ids'];

    public static $rules = [
        'title' => 'required',
        'summary' => 'max:255',
        'category_ids' => 'required|array|min:1',
        'category_ids.*' => 'exists:blogpost_categories,id',
    ];

    protected $filterableFields  = ['title', 'summary', 'text'];

    protected $defaultImage = "resources/images/icons/newspaper.png";
    

    //TODO Use https://github.com/spatie/laravel-sluggable
    public static function findBySlug($slug): ?Blogpost
    {

        $blogpost = self::where('slug', $slug)->first();

        if(!empty($blogpost)) {
            return $blogpost;
        } else {

            //FIXME Use find
            foreach (self::where('slug', null)->orWhere('slug', "")->get() as $blogpost) {
                if (str_slug($blogpost->title) == $slug) {
                    return $blogpost;
                }
            }
        }

        return null;
    }

    public function getCategoryIdsAttribute()
    {
        return $this->categories()->pluck('blogpost_categories.id'); 
    }

    //TODO Use local scope
    public static function getPublished($num = null, $order = 'ASC')
    {
        return self::active()->orderBy('created_at', $order)->paginate($num);
    }

    //TODO Use local scope
    public static function getDrafts($num = null, $order = 'ASC')
    {
        return self::inactive()->get()->orderBy('created_at', $order)->paginate($num);
    }

    //TODO Use local scope
    public static function getFeatured($num = null, $order = 'ASC')
    {
        return self::where('active', 2)->orderBy('created_at', $order)->paginate($num);
    }

    public function categories()
    {
        return $this->belongsToMany(BlogpostCategory::class, 'blogpost_categories_pivot');
    }

    public function comments()
    {
        return $this->hasMany(BlogpostComment::class, 'blogpost_id', 'id');
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function getSlug()
    {
        return empty($this->slug) ? str_slug($this->title) : $this->slug;
    }

    public function getExcerpt($char_num = 255)
    {

        return empty($this->summary) ? substr(strip_tags($this->text), 0, $char_num) : $this->summary;
    }

    public function getTotalCharacterCount(): int
    {
        return strlen(strip_tags($this->text));
    }

    public function getTotalWordCount(): int
    {
        return str_word_count(strip_tags($this->text));
    }

    public function getReadingTime(): int
    {
        return ($this->getTotalWordCount() / 200) * 60;
    }

    public function isPublished(): bool
    {
        return $this->isActive();
    }

    public function isFeatured(): bool
    {
        return $this->active == 2;
    }
}
