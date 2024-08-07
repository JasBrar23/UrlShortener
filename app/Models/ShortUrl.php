<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortUrl extends Model
{
    use SoftDeletes;

    protected $fillable = ['original_url', 'short_url', 'clicks', 'is_active'];
}
