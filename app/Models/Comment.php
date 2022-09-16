<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Get the subcomments
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function comments()
    {
        return $this->HasMany($this, 'parent_id');
    }
}
