<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappConnection extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'whatsapp_connection_status_id',
        'instance_name',
        'instance_id',
        'phone_number',
    ];

    public function whatsappConnectionStatus(): BelongsTo
    {
        return $this->belongsTo(WhatsappConnectionStatus::class);
    }
}
