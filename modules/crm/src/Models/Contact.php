<?php

namespace Moduloom\Crm\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'crm_contacts';

    protected $fillable = ['name', 'email', 'phone', 'notes'];
}
