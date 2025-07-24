<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Obtener el perfil del usuario autenticado
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        return response()->json(['data' => $user]);
    }

    /**
     * Actualizar el perfil del usuario autenticado
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user->update($validated);
        
        return response()->json([
            'message' => 'Perfil actualizado con éxito',
            'data' => $user
        ]);
    }
} 