<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    public $fillable = ["name"];


    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
