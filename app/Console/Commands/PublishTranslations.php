<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class PublishTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish translations and clear cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear cache
        Cache::flush();
        $this->info('Cache cleared.');
        
        // Publish translations
        $locales = ['en', 'ar'];
        
        foreach ($locales as $locale) {
            // Check JSON file
            $jsonPath = resource_path("lang/{$locale}.json");
            if (File::exists($jsonPath)) {
                try {
                    $content = File::get($jsonPath);
                    $translations = json_decode($content, true);
                    
                    if (is_array($translations)) {
                        $this->info("JSON translations for {$locale}: " . count($translations) . " entries");
                        
                        // Verify some keys
                        $testKeys = ['country_code', 'parent_category'];
                        foreach ($testKeys as $key) {
                            if (isset($translations[$key])) {
                                $this->info("✓ Key '{$key}' exists in {$locale}.json with value: " . $translations[$key]);
                            } else {
                                $this->error("✗ Key '{$key}' does not exist in {$locale}.json");
                            }
                        }
                    } else {
                        $this->error("Failed to decode JSON translations for {$locale}");
                    }
                } catch (\Exception $e) {
                    $this->error("Error processing {$locale}.json: " . $e->getMessage());
                }
            } else {
                $this->error("JSON file {$jsonPath} does not exist");
            }
            
            // Check PHP files
            $phpPath = resource_path("lang/{$locale}");
            if (File::isDirectory($phpPath)) {
                foreach (File::files($phpPath) as $file) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    try {
                        $translations = require $file->getPathname();
                        $this->info("PHP translations for {$locale}.{$filename}: " . count($translations) . " entries");
                    } catch (\Exception $e) {
                        $this->error("Error processing {$locale}/{$filename}.php: " . $e->getMessage());
                    }
                }
            }
        }
        
        $this->info('Translations published successfully.');
        
        return 0;
    }
} 