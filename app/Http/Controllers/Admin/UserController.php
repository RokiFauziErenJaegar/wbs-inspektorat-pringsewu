<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.user.index', [
            'users' => User::query()
                ->with('opd')
                ->withCount('laporan')
                ->when($request->string('q')->toString(), fn ($q, $kata) => $q->where(fn ($w) => $w
                    ->where('name', 'like', "%{$kata}%")
                    ->orWhere('username', 'like', "%{$kata}%")
                    ->orWhere('email', 'like', "%{$kata}%")))
                ->when($request->string('role')->toString(), fn ($q, $r) => $q->where('role', $r))
                ->when($request->string('opd')->toString(), fn ($q, $o) => $q->where('opd_id', $o))
                ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('is_active', $s === 'aktif'))
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString(),
            'opdList' => Opd::orderBy('nama')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.user.form', [
            'user' => new User(['role' => User::ROLE_PETUGAS, 'is_active' => true]),
            'opdList' => Opd::aktif()->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['is_active'] = $request->boolean('is_active');

        User::create($data);

        return redirect()->route('admin.user.index')->with('sukses', 'Pengguna baru ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.user.form', [
            'user' => $user,
            'opdList' => Opd::aktif()->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validasi($request, $user);
        $data['is_active'] = $request->boolean('is_active');

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        // Cegah administrator terakhir kehilangan aksesnya sendiri.
        if ($user->id === $request->user()->id && ($data['role'] !== User::ROLE_ADMIN || ! $data['is_active'])) {
            return back()->with('gagal', 'Anda tidak dapat menurunkan peran atau menonaktifkan akun sendiri.');
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('sukses', 'Data pengguna diperbarui.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('gagal', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('sukses', 'Status akun '.$user->name.' diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('gagal', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->laporan()->exists()) {
            return back()->with('gagal', 'Pengguna tidak dapat dihapus karena memiliki riwayat pengaduan. Nonaktifkan saja akunnya.');
        }

        $user->delete();

        return back()->with('sukses', 'Pengguna dihapus.');
    }

    private function validasi(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'min:4', 'max:60', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($user?->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_PETUGAS, User::ROLE_PELAPOR])],
            'opd_id' => [Rule::requiredIf($request->input('role') === User::ROLE_PELAPOR), 'nullable', Rule::exists('opd', 'id')],
            'nip' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->numbers()],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
        ], ['name' => 'nama lengkap', 'opd_id' => 'OPD']);
    }
}
