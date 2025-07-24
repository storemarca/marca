<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Tiempo de caché para las configuraciones (en segundos)
     */
    const CACHE_TIME = 86400; // 24 horas

    /**
     * Obtener todas las configuraciones de un grupo
     *
     * @param string $group
     * @return array
     */
    public function getGroup(string $group): array
    {
        $cacheKey = "settings_{$group}";
        
        return Cache::remember($cacheKey, self::CACHE_TIME, function () use ($group) {
            return Setting::where('group', $group)->get()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Obtener una configuración específica
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";
        
        return Cache::remember($cacheKey, self::CACHE_TIME, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Establecer una configuración
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return void
     */
    public function set(string $key, $value, string $group = 'general'): void
    {
        Setting::set($key, $value, $group);
        
        // Limpiar caché
        Cache::forget("setting_{$key}");
        Cache::forget("settings_{$group}");
    }

    /**
     * Establecer múltiples configuraciones a la vez
     *
     * @param array $settings
     * @param string $group
     * @return void
     */
    public function setMany(array $settings, string $group = 'general'): void
    {
        foreach ($settings as $key => $value) {
            Setting::set($key, $value, $group);
            Cache::forget("setting_{$key}");
        }
        
        Cache::forget("settings_{$group}");
    }

    /**
     * Verificar si existe una configuración
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Setting::where('key', $key)->exists();
    }

    /**
     * Eliminar una configuración
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            $group = $setting->group;
            $setting->delete();
            
            // Limpiar caché
            Cache::forget("setting_{$key}");
            Cache::forget("settings_{$group}");
        }
    }
}