<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Korwil extends Model
{
    use HasFactory;

    protected $fillable = [
        'region'
    ];

    public function members()
    {
        return $this->hasOne(Member::class);
    }
}
