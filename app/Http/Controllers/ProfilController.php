<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profil', [
            'user' => $request->user(),
            'opdList' => Opd::aktif()->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'nip' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'opd_id' => [$user->isPelapor() ? 'required' : 'nullable', Rule::exists('opd', 'id')],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ], [], ['name' => 'nama lengkap', 'opd_id' => 'OPD']);

        if ($berkas = $request->file('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $berkas->store('avatar', 'public');
        } else {
            unset($data['avatar']);
        }

        if ($user->email !== $data['email']) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        return back()->with('sukses', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'password_lama.current_password' => 'Kata sandi lama tidak sesuai.',
        ], [
            'password_lama' => 'kata sandi lama',
            'password' => 'kata sandi baru',
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('sukses', 'Kata sandi berhasil diganti.');
    }
}
