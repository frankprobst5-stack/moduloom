<?php

namespace Moduloom\Events\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $table = 'event_registrations';

    protected $fillable = ['event_id', 'attendee_name', 'email'];
}
