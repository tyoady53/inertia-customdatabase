<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extend extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function files()
    {
        return $this->hasMany(ExtendFile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
