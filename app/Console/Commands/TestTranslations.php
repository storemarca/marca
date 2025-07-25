<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

class TestTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:test {key?} {locale=ar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test translations and debug issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->argument('locale');
        $key = $this->argument('key');
        
        App::setLocale($locale);
        
        $this->info("Current locale: " . App::getLocale());
        $this->info("Fallback locale: " . config('app.fallback_locale'));
        
        // Check if language files exist
        $this->info("\nChecking language files:");
        $this->checkLanguageFiles($locale);
        
        // If a key is provided, test it
        if ($key) {
            $this->info("\nTesting translation for key: " . $key);
            $this->testTranslation($key, $locale);
        } else {
            // Test some common keys
            $this->info("\nTesting common translations:");
            $this->testTranslation('admin.dashboard', $locale);
            $this->testTranslation('admin.products', $locale);
            $this->testTranslation('admin.language_settings', $locale);
            $this->testTranslation('country_code', $locale);
            $this->testTranslation('parent_category', $locale);
        }
    }
    
    /**
     * Check if language files exist
     */
    private function checkLanguageFiles($locale)
    {
        // Check PHP files
        $phpPath = lang_path($locale);
        if (File::isDirectory($phpPath)) {
            $this->info("✓ PHP language directory exists: " . $phpPath);
            
            // Check admin.php
            $adminFile = $phpPath . '/admin.php';
            if (File::exists($adminFile)) {
                $this->info("✓ admin.php exists");
                $translations = include $adminFile;
                $this->info("  - Contains " . count($translations) . " translations");
            } else {
                $this->error("✗ admin.php does not exist");
            }
        } else {
            $this->error("✗ PHP language directory does not exist: " . $phpPath);
        }
        
        // Check JSON file
        $jsonFile = lang_path($locale . '.json');
        if (File::exists($jsonFile)) {
            $this->info("✓ JSON language file exists: " . $jsonFile);
            $translations = json_decode(File::get($jsonFile), true);
            if ($translations) {
                $this->info("  - Contains " . count($translations) . " translations");
            } else {
                $this->error("  - JSON file is invalid or empty");
            }
        } else {
            $this->error("✗ JSON language file does not exist: " . $jsonFile);
        }
    }
    
    /**
     * Test a translation key
     */
    private function testTranslation($key, $locale)
    {
        // Test with trans()
        $transFn = trans($key, [], $locale);
        $this->info("trans(): " . ($transFn === $key ? "Not found" : $transFn));
        
        // Test with __()
        $underscoreFn = __($key, [], $locale);
        $this->info("__(): " . ($underscoreFn === $key ? "Not found" : $underscoreFn));
        
        // Test with safe_trans()
        $safeTrans = safe_trans($key, [], $locale);
        $this->info("safe_trans(): " . $safeTrans);
        
        // Check if key contains a dot
        if (strpos($key, '.') !== false) {
            list($file, $item) = explode('.', $key, 2);
            $this->info("Checking for key in {$file}.php file:");
            
            $phpPath = lang_path($locale . '/' . $file . '.php');
            if (File::exists($phpPath)) {
                $translations = include $phpPath;
                if (isset($translations[$item])) {
                    $this->info("✓ Key exists in file with value: " . $translations[$item]);
                } else {
                    $this->error("✗ Key does not exist in file");
                }
            } else {
                $this->error("✗ File does not exist: " . $phpPath);
            }
        } else {
            // Check in JSON file
            $jsonFile = lang_path($locale . '.json');
            if (File::exists($jsonFile)) {
                $translations = json_decode(File::get($jsonFile), true);
                if (isset($translations[$key])) {
                    $this->info("✓ Key exists in JSON file with value: " . $translations[$key]);
                } else {
                    $this->error("✗ Key does not exist in JSON file");
                }
            }
        }
    }
} 