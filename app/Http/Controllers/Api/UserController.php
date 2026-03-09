<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * GET /api/users
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::with(['roles:name', 'boutique:id,nom'])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->boutique_id, fn($q) => $q->where('boutique_id', $request->boutique_id))
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return response()->json($users);
    }

    /**
     * POST /api/users
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'phone'       => 'nullable|string|max:20',
            'password'    => 'required|string|min:8|confirmed',
            'role'        => 'required|in:admin,gestionnaire,vendeur',
            'boutique_id' => 'nullable|exists:boutiques,id',
        ]);

        $role = $validated['role'];
        unset($validated['role']);
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->assignRole($role);

        return response()->json([
            'message' => 'Utilisateur créé.',
            'user'    => $user->load(['roles', 'boutique']),
        ], 201);
    }

    /**
     * GET /api/users/{id}
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user->load(['roles', 'boutique']));
    }

    /**
     * PUT /api/users/{id}
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'boutique_id' => 'nullable|exists:boutiques,id',
            'is_active'   => 'boolean',
            'role'        => 'sometimes|in:admin,gestionnaire,vendeur',
        ]);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
            unset($validated['role']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Utilisateur mis à jour.',
            'user'    => $user->fresh(['roles', 'boutique']),
        ]);
    }

    /**
     * DELETE /api/users/{id}
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé.']);
    }

    /**
     * PUT /api/users/{id}/reset-password
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Mot de passe réinitialisé.']);
    }
}
