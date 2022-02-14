<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public $fillable = ['title', 'description', 'publisher_id'];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
}
