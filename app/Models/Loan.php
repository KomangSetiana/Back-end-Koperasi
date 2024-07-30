<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'loan_amount', 'date', 'time_period', 'created_by'];


    public function members()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function installments()
    {
        return $this->hasMany(Installments::class, 'loan_id');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeFillter($query, $request)
    {
        $query->when($request->search, function ($query) use ($request) {
            $query->where('date', 'like', '%' . $request->search . '%')
                ->orWhereHas('members', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                });
        });
    }
}
