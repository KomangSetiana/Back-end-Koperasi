<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportLoan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'date', 'interest', 'principal', 'mulct', 'amount', 'remainning', 'created_by', 'is_acc'];


    public function users()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
