<?php

namespace App\Model\Trait;
 
trait Draftable {

    public function isDraft(): bool
    {
        return $this->active == 0;
    }

}