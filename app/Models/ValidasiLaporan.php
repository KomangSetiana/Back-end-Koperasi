<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidasiLaporan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'validasi_laporan_id');
    }

    public function scopeFillter($query, $request)
    {
        $query->when($request->search, function ($query) use ($request) {
            $query->where('type', 'like', '%' . $request->search . '%');
        });
    }
}
