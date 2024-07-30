<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installments extends Model
{
    use HasFactory;

    protected $fillable = ['loan_id', 'date', 'amount_payment', 'principal_loan', 'bunga_pinjaman', 'remaining_debt', 'created_by', "mulct"];



    public function loans()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'created_by');
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
                ->orWhereHas('loans.members', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                });
        });
    }
}
