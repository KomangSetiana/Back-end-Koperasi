<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'korwil_id',
        'gender',
        'address',
        'telp',
        'user_id'
    ];

    public function korwil()
    {
        return $this->belongsTo(Korwil::class);
    }

    public function loans()
    {
        return $this->hasOne(Loan::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFillter($query, $request)
    {
        $query->when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
            $query->orWhere('address', 'like', '%' . $request->search . '%')
                ->orWhereHas('korwil', function ($query) use ($request) {
                    $query->where('region', 'like', '%' . $request->search . '%');
                });
        });
    }
}
