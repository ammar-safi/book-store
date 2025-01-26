<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "title",
        "uuid",
        "author",
        "cover",
        "description",
        "book",
        "user_id",
        "created_at",
    ];

    // auto generate uuid 
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) \Str::uuid();
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
