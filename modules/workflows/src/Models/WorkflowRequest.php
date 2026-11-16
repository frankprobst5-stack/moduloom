<?php

namespace Moduloom\Workflows\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowRequest extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'requested_by', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
