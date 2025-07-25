<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    /**
     * Display the language settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = [
            'default_language' => setting('default_language', config('app.locale')),
            'show_language_switcher' => setting('show_language_switcher', '1'),
            'cache_translations' => setting('cache_translations', '1'),
            'fallback_locale' => setting('fallback_locale', config('app.fallback_locale')),
        ];
        
        return view('admin.settings.language', compact('settings'));
    }
    
    /**
     * Update the language settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'default_language' => 'required|in:ar,en',
            'show_language_switcher' => 'required|in:0,1',
            'cache_translations' => 'required|in:0,1',
            'fallback_locale' => 'required|in:ar,en',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Update settings
        $this->updateSetting('default_language', $request->default_language);
        $this->updateSetting('show_language_switcher', $request->show_language_switcher);
        $this->updateSetting('cache_translations', $request->cache_translations);
        $this->updateSetting('fallback_locale', $request->fallback_locale);
        
        // Update config values
        config(['app.locale' => $request->default_language]);
        config(['app.fallback_locale' => $request->fallback_locale]);
        
        // Clear translation cache
        $this->clearTranslationCache();
        
        return redirect()->back()->with('success', safe_trans('admin.settings_updated_successfully'));
    }
    
    /**
     * Get translations for a specific file and locale.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTranslations(Request $request)
    {
        $file = $request->query('file', 'admin');
        $locale = $request->query('locale', 'ar');
        
        if (!in_array($locale, ['ar', 'en'])) {
            return response()->json(['error' => 'Invalid locale'], 400);
        }
        
        if ($file === 'admin') {
            $path = lang_path($locale . '/admin.php');
        } elseif ($file === 'frontend') {
            $path = lang_path($locale . '.json');
        } else {
            return response()->json(['error' => 'Invalid file'], 400);
        }
        
        if (!File::exists($path)) {
            return response()->json([], 200);
        }
        
        if ($file === 'admin') {
            $translations = include $path;
        } else {
            $translations = json_decode(File::get($path), true);
        }
        
        return response()->json($translations);
    }
    
    /**
     * Save translations for a specific file and locale.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveTranslations(Request $request)
    {
        $file = $request->input('file', 'admin');
        $locale = $request->input('locale', 'ar');
        $translations = $request->input('translations', []);
        
        if (!in_array($locale, ['ar', 'en'])) {
            return response()->json(['error' => 'Invalid locale'], 400);
        }
        
        if ($file === 'admin') {
            $path = lang_path($locale . '/admin.php');
            $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
        } elseif ($file === 'frontend') {
            $path = lang_path($locale . '.json');
            $content = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json(['error' => 'Invalid file'], 400);
        }
        
        try {
            File::put($path, $content);
            $this->clearTranslationCache();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Clear the translation cache.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache(Request $request)
    {
        try {
            $this->clearTranslationCache();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Clear the translation cache.
     */
    private function clearTranslationCache()
    {
        $locales = ['en', 'ar'];
        
        foreach ($locales as $locale) {
            Cache::forget('translations_json_' . $locale);
            Cache::forget('translations_php_' . $locale);
        }
        
        if (app()->environment('production')) {
            Artisan::call('cache:clear');
        }
    }
    
    /**
     * Update a setting.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    private function updateSetting($key, $value)
    {
        $setting = \App\Models\Setting::firstOrNew(['key' => $key]);
        $setting->value = $value;
        if (!$setting->exists) {
            $setting->group = 'language';
        }
        $setting->save();
        
        // Update cache
        Cache::put('setting_' . $key, $value, 60 * 24);
    }
} 