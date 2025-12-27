<?php

namespace App\Model\Trait;

use Illuminate\Database\Eloquent\Builder;
 
trait IsActive {

    public function activate(): void {
        $this->active = 1;
    }

    public function deactivate(): void {
        $this->active = 0;
    }

    public function isActive(): bool {
        return $this->active > 0;
    }

    public function isInactive(): bool {
        return $this->active == null || $this->active == 0;
    }

    public function scopeActive(Builder $query): Builder {
        return $query->where('active', '>', 0);
    }

    public function scopeInActive(Builder $query): Builder {
        return $query->where('active', 0);
    }

}