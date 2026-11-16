<?php

namespace Moduloom\Invoicing\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Moduloom\Crm\Models\Contact;

class Invoice extends Model
{
    protected $fillable = ['crm_contact_id', 'user_id', 'customer_name', 'amount_cents', 'status'];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'crm_contact_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
