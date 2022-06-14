<?php

namespace Test\Querial\MockModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}