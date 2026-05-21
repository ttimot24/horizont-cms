<?php

namespace App\Model;

use App\Model\Trait\HasAuthor;
use App\Model\Trait\IsActive;
use Illuminate\Database\Eloquent\Model;

class ScheduledTask extends Model
{
    use HasAuthor;
    use IsActive;

    protected $table = 'schedules';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'command', 'arguments', 'frequency', 'ping_before', 'ping_after', 'author_id', 'active',
    ];
}
