<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user
     */
    public function index(Request $request)
    {
        $q      = $request->query('q');
        $role   = $request->query('role');
        $active = $request->query('active');

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($role, fn($query) => $query->where('role', $role))
            ->when($active !== null && $active !== '', fn($query) => $query->where('active', $active))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'role', 'active'));
    }

    /**
     * Form tambah user
     */
    public function create()
    {
        return view('admin.users.form', [
            'user'   => new User(),
            'title'  => 'Tambah User',
            'mode'   => 'create',
            'method' => 'POST',
            'action' => route('admin.users.store'),
        ]);
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'role'     => ['required', Rule::in(['admin', 'petugas'])],
            'active'   => ['nullable', 'boolean'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = new User();
        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->role     = $data['role'];
        $user->active   = $request->boolean('active');
        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('ok', 'User berhasil ditambahkan.');
    }

    /**
     * Form edit user
     */
    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user'   => $user,
            'title'  => 'Edit User',
            'mode'   => 'edit',
            'method' => 'PUT',
            'action' => route('admin.users.update', $user),
        ]);
    }

    /**
     * Update data user
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users','email')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin', 'petugas'])],
            'active'   => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->name   = $data['name'];
        $user->email  = $data['email'];
        $user->role   = $data['role'];
        $user->active = $request->boolean('active');

        // Jika password diisi, update
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('ok', 'User berhasil diupdate.');
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        // Tidak boleh menghapus akun sendiri
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // Opsional tapi penting: Jangan sampai admin terakhir dihapus
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak boleh menghapus admin terakhir.');
            }
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('ok', 'User berhasil dihapus.');
    }
}
