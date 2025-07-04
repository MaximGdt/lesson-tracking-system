<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the user's profile form.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'locale' => ['required', 'in:uk,en'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($request->user()->avatar) {
                Storage::disk('public')->delete($request->user()->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->route('profile.edit')->with('success', __('app.profile_updated'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', __('app.password_changed'));
    }
    
    /**
     * Delete user avatar.
     */
    public function deleteAvatar(Request $request)
    {
        if ($request->user()->avatar) {
            Storage::disk('public')->delete($request->user()->avatar);
            $request->user()->update(['avatar' => null]);
        }
        
        return back()->with('success', __('app.avatar_deleted'));
    }
}
