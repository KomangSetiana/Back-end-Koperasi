<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDeposit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'date', 'debet', 'kredit', 'saldo', 'deposit_type_id', 'created_by'];

    public function users()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
