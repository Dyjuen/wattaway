<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Esp32Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content',
        'direction',
        'metadata',
        'ip_address',
        'arduino_time',
        'led_state',
        'endpoint',
        'user_agent',
        'payload',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'arduino_time' => 'datetime',
    ];

    /**
     * Scope a query to only include incoming messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncoming($query)
    {
        return $query->where('direction', 'incoming');
    }

    /**
     * Scope a query to only include outgoing messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'outgoing');
    }

    /**
     * Scope a query to only include messages since a given timestamp.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $timestamp
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSince($query, $timestamp)
    {
        return $query->where('created_at', '>', date('Y-m-d H:i:s', $timestamp));
    }

    /**
     * Create a new incoming message from ESP32.
     *
     * @param  string  $content
     * @param  array  $metadata
     * @return \App\Models\Esp32Message
     */
    public static function createIncoming($content, $metadata = [])
    {
        // Extract arduino_time and led_state from metadata if they exist
        $arduinoTime = $metadata['arduino_time'] ?? null;
        $ledState = $metadata['led_state'] ?? null;
        
        // Remove these from metadata to avoid duplication
        unset($metadata['arduino_time'], $metadata['led_state']);
        
        return static::create([
            'content' => is_string($content) ? $content : json_encode($content),
            'direction' => 'incoming',
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
            'arduino_time' => $arduinoTime,
            'led_state' => $ledState,
        ]);
    }

    /**
     * Create a new outgoing message to ESP32.
     *
     * @param  string  $content
     * @param  array  $metadata
     * @return \App\Models\Esp32Message
     */
    public static function createOutgoing($content, $metadata = [])
    {
        return static::create([
            'content' => is_string($content) ? $content : json_encode($content),
            'direction' => 'outgoing',
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
        ]);
    }
}
