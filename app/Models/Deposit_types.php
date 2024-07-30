<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit_types extends Model
{
    use HasFactory;

    protected  $fillable = ['deposit_name'];

    public function deposits()
    {
        return $this->hasOne(Deposit::class);
    }
}
