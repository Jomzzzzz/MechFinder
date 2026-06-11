<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 */
class Message extends Model
{
    protected $fillable = [
        'dispatch_id',
        'motorist_id',
        'shop_id',
        'mechanic_id',
        'message',
        'is_read',
        'sender_type',
        'sender_name',
        'conversation_type',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function dispatch()
    {
        return $this->belongsTo(DispatchRequest::class, 'dispatch_id');
    }

    public function motorist()
    {
        return $this->belongsTo(User::class, 'motorist_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function getSenderNameAttribute()
    {
        return match ($this->sender_type) {
            'motorist' => $this->motorist?->name ?? 'Motorist',
            'shop' => $this->shop?->name ?? 'Shop',
            'mechanic' => $this->mechanic?->name ?? 'Mechanic',
            default => 'Unknown',
        };
    }
}
