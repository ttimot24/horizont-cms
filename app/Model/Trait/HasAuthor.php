<?php

namespace App\Model\Trait;

trait HasAuthor
{
    /*
    * In db it has to be author_id else it won't work because Laravel priority is attr -> function
    */
    public function author()
    {
        return $this->belongsTo(\App\Model\User::class, 'author_id', 'id');
    }
}
