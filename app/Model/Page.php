<?php

namespace App\Model;

use App\Model\Trait\HasAuthor;
use App\Model\Trait\HasImage;
use App\Model\Trait\PaginateSortAndFilter;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasAuthor;
    use HasImage;
    use PaginateSortAndFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'url', 'visibility', 'parent_id', 'language', 'queue', 'page', 'active',
    ];

    public static $rules = [
        'name' => 'required',
        'visibility' => 'required',
    ];

    public $casts = [
        'parent_id' => 'int',
        'visibility' => 'int',
    ];

    protected $filterableFields = ['name', 'page'];

    protected $defaultImage = 'resources/images/icons/page.png';

    // TODO Use local scope
    public static function home()
    {
        return self::find(Settings::get('home_page'));
    }

    public static function findBySlug($slug)
    {

        $page = self::where('slug', $slug)->first();

        if (! empty($page)) {
            return $page;
        } else {

            foreach (self::where('slug', null)->orWhere('slug', '')->get() as $page) {
                if (str_slug($page->name) == $slug) {
                    return $page;
                }
            }

        }

        return null;
    }

    public function scopeWithTemplate($query, $template)
    {
        return $query->where('url', $template);
    }

    public function scopeMain($query)
    {
        return $query->where('parent_id', null)->orderBy('queue')->orderBy('id');
    }

    public function scopeActive($query)
    {
        return $query->where('visibility', 1)->orderBy('queue')->orderBy('id');
    }

    public function scopeLanguage($query, $lang)
    {
        return $query->where('language', $lang)->orderBy('queue')->orderBy('id');
    }

    public function isActive()
    {
        return $this->visibility == 1;
    }

    public function isParent()
    {
        return $this->parent_id == null;
    }

    public function hasSubpages()
    {
        return $this->subpages->count() > 0;
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function subpages()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function getSlug()
    {
        return empty($this->slug) ? str_slug($this->name) : $this->slug;
    }
}
