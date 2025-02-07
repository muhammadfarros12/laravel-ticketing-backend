<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTicket extends Model
{
    protected $fillable = [
        'order_id',
        'ticket_id',
        'user_id',
        'event_id',
        'quantity',
        'total',
        'status',
        'event_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // sku
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
