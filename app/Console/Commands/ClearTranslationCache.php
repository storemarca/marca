<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ClearTranslationCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all translation cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get all supported locales
        $locales = ['en', 'ar']; // Add all your supported locales here
        
        // Clear cache for each locale
        foreach ($locales as $locale) {
            Cache::forget('translations_json_' . $locale);
            Cache::forget('translations_php_' . $locale);
            
            // Clear individual translation keys
            $keys = Cache::get('translation_keys_' . $locale, []);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
            Cache::forget('translation_keys_' . $locale);
        }
        
        $this->info('Translation cache cleared successfully!');
        
        return 0;
    }
} 