<?php

namespace Addons\PendingClients\Models;

use Addons\PendingClients\PendingClientsFormatter;
use FI\Support\DateFormatter;
use Illuminate\Database\Eloquent\Model;

class ClientPending extends Model
{
    protected $table = 'clients_pending';

    protected $primaryKey = 'client_id';

    public $timestamps = false;

    public $dates = ['created_at'];

    public function getFormattedCreatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['created_at']);
    }

    public function getPhoneAttribute($value)
    {
        return PendingClientsFormatter::phone($value);
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function getAddressAttribute($value)
    {
        return ucwords($value);
    }

    public function getCityAttribute($value)
    {
        return ucwords($value);
    }
}