<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'description',
        'profile_photo',
        'llm_api_key',
        'llm_service_name',
        'comfyui_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'llm_api_key',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function adminlte_image()
    {
        return $this->profile_photo ?? 'https://picsum.photos/300/300';
    }

    public function adminlte_desc()
    {
        return $this->description ?? 'I\'m a nice guy';
    }

    public function adminlte_profile_url()
    {
        return route('profile.show');
    }

    public function getLlmApiKeyAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Crypt::decryptString($value);
    }

    public function setLlmApiKeyAttribute($value)
    {
        $this->attributes['llm_api_key'] = Crypt::encryptString($value);
    }

    public function getMaskedLlmApiKeyAttribute()
    {
        $apiKey = $this->llm_api_key;

        if (empty($apiKey)) {
            return null;
        }

        $length = strlen($apiKey);
        $visibleLength = 4; // Número de caracteres visibles al inicio y al final
        $maskedLength = $length - (2 * $visibleLength);

        if ($maskedLength <= 0) {
            return '********'; // Si la API key es muy corta, no se enmascara
        }

        $maskedApiKey = substr($apiKey, 0, $visibleLength) . str_repeat('*', 8) . substr($apiKey, -$visibleLength);

        return $maskedApiKey;
    }

    public function getComfyuiUrlAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Crypt::decryptString($value);
    }

    public function setComfyuiUrlAttribute($value)
    {
        $this->attributes['comfyui_url'] = Crypt::encryptString($value);
    }
}