<?php

namespace Vendor\Heimdall\Models;

use Illuminate\Database\Eloquent\Model;

class LlmLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'raw_payload' => 'array',
        'is_success' => 'boolean',
    ];
}
