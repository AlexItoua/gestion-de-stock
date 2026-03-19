<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     * Connexion par numéro de téléphone + mot de passe
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone'       => 'required|string',
            'password'    => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $phone        = $this->normalizePhone($request->phone);
        $phoneFormate = $this->formatPhone($phone);

        // 🔍 Recherche utilisateur
        $user = User::where('phone', $phone)
                    ->orWhere('phone', $phoneFormate)
                    ->first();

        // ❌ 1. Numéro introuvable
        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => ['Ce numéro n\'est lié à aucun compte.'],
            ]);
        }

        // ❌ 2. Mot de passe incorrect
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Le mot de passe inséré est incorrect.'],
            ]);
        }

        // ❌ 3. Compte désactivé
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Votre compte est désactivé. Contactez l\'administrateur.',
            ], 403);
        }

        // ✅ Génération token
        $token = $user->createToken($request->device_name ?? 'api-token');

        return response()->json([
            'message' => 'Connexion réussie',
            'token'   => $token->plainTextToken,
            'user'    => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'roles'       => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'boutique'    => $user->boutique?->only(['id', 'nom', 'code']),
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()
            ->tokens()
            ->where('id', $request->user()->currentAccessToken()->id)
            ->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    /**
     * GET /api/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('boutique');

        return response()->json([
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'roles'       => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'boutique'    => $user->boutique?->only(['id', 'nom', 'code', 'type']),
                'is_active'   => $user->is_active,
                'created_at'  => $user->created_at,
            ],
        ]);
    }

    /**
     * PUT /api/auth/password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe actuel incorrect.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Mot de passe modifié avec succès.'
        ]);
    }

    // ─── Helpers téléphone ─────────────────────────────────────

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/[\s\-\.]/', '', $phone);
    }

    private function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '242')) {
            $digits = substr($digits, 3);
        }

        if (strlen($digits) === 9) {
            return '+242 ' . substr($digits, 0, 2) . ' '
                           . substr($digits, 2, 3) . ' '
                           . substr($digits, 5, 2) . ' '
                           . substr($digits, 7, 2);
        }

        return $phone;
    }
}
