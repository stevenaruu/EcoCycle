<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHeader extends Model
{
    protected $table = 'transaction_headers';
    protected $guarded = [];
    protected $fillable = [
        'status',
        'buyer_id',
        'total_price',
        'shipping_fee',
        'grand_total',
        'snap_token',
        'address_id',
        'payment_method',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
