<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    protected $settingService;
    
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }
    
    /**
     * Mostrar configuraciones generales
     */
    public function general()
    {
        $settings = $this->settingService->getGroup('general');
        return view('admin.settings.general', compact('settings'));
    }
    
    /**
     * Guardar configuraciones generales
     */
    public function saveGeneral(Request $request)
    {
        $settings = $request->except('_token', '_method');
        
        // Manejar carga de archivos
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('settings', 'public');
            $settings['site_logo'] = $path;
        }
        
        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('settings', 'public');
            $settings['site_favicon'] = $path;
        }
        
        $this->settingService->setMany($settings, 'general');
        
        return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
    
    /**
     * Mostrar configuraciones de la página de inicio
     */
    public function homepage()
    {
        $settings = $this->settingService->getGroup('homepage');
        return view('admin.settings.homepage', compact('settings'));
    }
    
    /**
     * Guardar configuraciones de la página de inicio
     */
    public function saveHomepage(Request $request)
    {
        $settings = $request->except('_token', '_method');
        
        // Manejar carga de archivos
        if ($request->hasFile('home_banner_image')) {
            $path = $request->file('home_banner_image')->store('settings', 'public');
            $settings['home_banner_image'] = $path;
        }
        
        $this->settingService->setMany($settings, 'homepage');
        
        return redirect()->back()->with('success', 'تم حفظ إعدادات الصفحة الرئيسية بنجاح');
    }
    
    /**
     * Mostrar configuraciones de tema
     */
    public function theme()
    {
        $settings = $this->settingService->getGroup('theme');
        return view('admin.settings.theme', compact('settings'));
    }
    
    /**
     * Guardar configuraciones de tema
     */
    public function saveTheme(Request $request)
    {
        $settings = $request->except('_token', '_method');
        $this->settingService->setMany($settings, 'theme');
        
        return redirect()->back()->with('success', 'تم حفظ إعدادات الثيمات بنجاح');
    }
    
    /**
     * Mostrar configuraciones de correo electrónico
     */
    public function mail()
    {
        $settings = $this->settingService->getGroup('mail');
        return view('admin.settings.mail', compact('settings'));
    }
    
    /**
     * Guardar configuraciones de correo electrónico
     */
    public function saveMail(Request $request)
    {
        $settings = $request->except('_token', '_method');
        $this->settingService->setMany($settings, 'mail');
        
        return redirect()->back()->with('success', 'تم حفظ إعدادات البريد الإلكتروني بنجاح');
    }
    
    /**
     * Mostrar configuraciones de envío
     */
    public function shipping()
    {
        $settings = $this->settingService->getGroup('shipping');
        return view('admin.settings.shipping', compact('settings'));
    }
    
    /**
     * Guardar configuraciones de envío
     */
    public function saveShipping(Request $request)
    {
        $settings = $request->except('_token', '_method');
        $this->settingService->setMany($settings, 'shipping');
        
        return redirect()->back()->with('success', 'تم حفظ إعدادات الشحن بنجاح');
    }
    
    /**
     * Mostrar configuraciones de pago
     */
    public function payment()
    {
        $settings = $this->settingService->getGroup('payment');
        return view('admin.settings.payment', compact('settings'));
    }
    
    /**
     * Guardar configuraciones de pago
     */
    public function savePayment(Request $request)
    {
        $settings = $request->except('_token', '_method');
        $this->settingService->setMany($settings, 'payment');
        
        return redirect()->back()->with('success', 'تم حفظ إعدادات الدفع بنجاح');
    }
} 