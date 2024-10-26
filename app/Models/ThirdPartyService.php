<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyService extends Model
{
    use HasFactory;

    protected $table = 'third_party_services';

    protected $fillable = [
        'service_name',
        'is_paused',
        'resume_at',
        'pause_reason',
        'retry_count',
    ];

    /**
     * Comprueba si el servicio está actualmente en pausa.
     *
     * @return bool
     */
    public function isCurrentlyPaused()
    {
        return $this->is_paused && (!$this->resume_at || now()->lt($this->resume_at));
    }
    
    /**
     * Pausa el servicio por un tiempo específico.
     *
     * @param string $reason
     * @param int $minutes
     */
    public function pause($reason, $minutes = 0)
    {
        $this->update([
            'is_paused' => true,
            'resume_at' => $minutes > 0 ? now()->addMinutes($minutes) : null,
            'pause_reason' => $reason,
        ]);
    }
    
    /**
     * Reanuda el servicio, permitiendo la ejecución de trabajos.
     */
    public function resume()
    {
        $this->update([
            'is_paused' => false,
            'resume_at' => null,
            'pause_reason' => null,
            'retry_count' => 0,
        ]);
    }
}
