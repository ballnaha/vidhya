<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'users' => $this->users($request->string('search')->toString()),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
        ]);

        $user = User::create($validated);

        return response()->json([
            'message' => __('User created.'),
            'user' => $this->serializeUser($user),
        ], 201);
    }

    public function checkEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'ignore' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $exists = User::query()
            ->where('email', $validated['email'])
            ->when($validated['ignore'] ?? null, fn ($query, $id) => $query->whereKeyNot($id))
            ->exists();

        return response()->json([
            'exists' => $exists,
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
        ]);

        if ($request->user()?->is($user) && $validated['role'] !== User::ROLE_ADMIN) {
            return response()->json([
                'message' => __('You cannot remove your own admin role.'),
                'errors' => [
                    'role' => [__('You cannot remove your own admin role.')],
                ],
            ], 422);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        return response()->json([
            'message' => __('User updated.'),
            'user' => $this->serializeUser($user),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()?->is($user)) {
            return response()->json([
                'message' => __('You cannot delete your own account.'),
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => __('User deleted.'),
        ]);
    }

    private function users(string $search = ''): array
    {
        return User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (User $user) => $this->serializeUser($user))
            ->all();
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'initials' => $user->initials(),
            'created_at' => $user->created_at?->format('M j, Y'),
        ];
    }
}
