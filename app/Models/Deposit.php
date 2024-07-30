<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;



    protected $fillable = ['member_id', 'date', 'debet', 'kredit', 'saldo', 'deposit_type_id', 'created_by'];


    // protected $attributes = ['created_by' => 2];
    public function members()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function deposit_types()
    {
        return $this->belongsTo(Deposit_types::class, 'deposit_type_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function validasi()
    {
        return $this->belongsTo(ValidasiLaporan::class, 'validasi_laporan_id');
    }
    public function scopeFillterDate($query, $dateStart, $dateEnd)
    {
        $query->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereDate('date', '>=', $dateStart)
                ->whereDate('date', '<=', $dateEnd);
        });
    }
    public function scopeFillter($query, $request)
    {
        $query->when($request->search, function ($query) use ($request) {
            $query->where('date', 'like', '%' . $request->search . '%')
                ->orWhereHas('members', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                })->orWhereHas('deposit_types', function ($query) use ($request) {
                    $query->where('deposit_name', 'like', '%' . $request->search . '%');
                });
        });
    }
    public function scopeFillterDepo($query, $fillDeposit)
    {
        $query->when($fillDeposit, function ($query) use ($fillDeposit) {
            $query->where('deposit_type_id', $fillDeposit);
        });
    }
}
