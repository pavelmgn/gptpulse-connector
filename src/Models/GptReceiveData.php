<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Models;

use Illuminate\Database\Eloquent\Model;

class GptReceiveData extends Model
{
    protected $fillable = [
        'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];


}
