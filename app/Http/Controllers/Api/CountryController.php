<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Obtener todos los países
     */
    public function index()
    {
        $countries = Country::where('is_active', true)->get();
        return response()->json(['data' => $countries]);
    }

    /**
     * Obtener un país específico
     */
    public function show($id)
    {
        $country = Country::findOrFail($id);
        return response()->json(['data' => $country]);
    }
} 