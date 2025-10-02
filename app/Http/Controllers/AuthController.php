<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'national_id' => ['required', 'string', 'max:255', 'unique:users,national_id'],
                'phone' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:500'],
                'summary' => ['nullable', 'string'],
                'link' => ['nullable', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:6'],
                'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'type' => ['required', 'string', 'in:user,agent,admin'],
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'national_id' => $validated['national_id'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'summary' => $validated['summary'] ?? null,
                'link' => $validated['link'] ?? null,
                'password' => Hash::make($validated['password']),
                'type' => $validated['type'],
                'is_active' => true,
            ]);

            $token = $user->createToken('api')->plainTextToken;

            if ($request->hasFile('avatar')) {
                $user->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ], 201);
        } catch (Throwable $e) {
            Log::error('Register failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'national_id' => ['required', 'string'],
                'password' => ['required', 'string'],
                'type' => ['required', 'string', 'in:user,agent,admin'],
            ]);

            $user = User::where('national_id', $credentials['national_id'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password) || $user->type !== $credentials['type']) {
                return response()->json([
                    'message' => 'Invalid credentials.',
                ], 422);
            }

            if (!$user->is_active) {
                return response()->json([
                    'message' => 'Account is inactive.',
                ], 403);
            }

            $token = $user->createToken('api')->plainTextToken;

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ]);
        } catch (Throwable $e) {
            Log::error('Login failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Login failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out.']);
        } catch (Throwable $e) {
            Log::error('Logout failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Logout failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            return new UserResource($request->user());
        } catch (Throwable $e) {
            Log::error('Profile fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch profile', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'national_id' => [
                    'sometimes', 'string', 'max:255',
                    Rule::unique('users', 'national_id')->ignore($user->id),
                ],
                'phone' => ['sometimes', 'string', 'max:255'],
                'address' => ['sometimes', 'string', 'max:500'],
                'summary' => ['nullable', 'string'],
                'link' => ['nullable', 'string', 'max:255'],
                'password' => ['nullable', 'string', 'min:6'],
                'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ]);

            if (array_key_exists('link', $validated) && $validated['link']) {
                $validated['link'] = $validated['link'];
            } else {
                unset($validated['link']);
            }

            if (array_key_exists('password', $validated) && $validated['password']) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            if ($request->hasFile('avatar')) {
                $user->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            $user->update($validated);

            return new UserResource($user->fresh());
        } catch (Throwable $e) {
            Log::error('Profile update failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Profile update failed', 'error' => $e->getMessage()], 500);
        }
    }

    // change password
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'password' => ['required', 'string', 'min:6'],
                'new_password' => ['required', 'string', 'min:6'],
            ]);

            $user = auth()->user();
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid password'], 401);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json(['message' => 'Password changed successfully', 'user' => new UserResource($user->fresh())]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Password change failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function uploadAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ]);

            $user = $request->user();
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');

            return new UserResource($user->fresh());
        } catch (Throwable $e) {
            Log::error('Avatar upload failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Avatar upload failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteAvatar(Request $request)
    {
        try {
            $user = $request->user();
            $media = $user->getFirstMedia('avatar');
            if ($media) {
                $media->delete();
            }
            return response()->json(['message' => 'Avatar deleted', 'user' => new UserResource($user->fresh())]);
        } catch (Throwable $e) {
            Log::error('Avatar delete failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Avatar delete failed', 'error' => $e->getMessage()], 500);
        }
    }
}


