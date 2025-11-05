<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the prompt qualities for this client.
     */
    public function promptQualities()
    {
        return $this->hasMany(PromptQuality::class, 'client_uuid', 'uuid');
    }
}

