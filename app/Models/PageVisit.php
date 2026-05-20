<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'visitor_hash',
    'ip_address',
    'ip_hash',
    'city',
    'country',
    'device_type',
    'browser',
    'platform',
    'screen_width',
    'screen_height',
    'language',
    'timezone',
    'user_agent_hash',
    'site_host',
    'page_title',
    'page_url',
    'referrer',
    'occurred_at',
])]
#[Hidden(['ip_address', 'ip_hash', 'visitor_hash', 'user_agent_hash'])]
class PageVisit extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ip_address' => 'encrypted',
            'occurred_at' => 'immutable_datetime',
        ];
    }
}
