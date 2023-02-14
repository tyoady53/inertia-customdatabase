<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAssignment extends Model
{
    use HasFactory;
    protected $table = 'master_assignment';
    protected $guarded = [
        'id',
    ];
}