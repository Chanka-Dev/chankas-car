<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empleado;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('empleado')
            ->orderBy('name')
            ->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $empleados = Empleado::with('cargo')->get();
        return view('users.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,tecnico,cajero,lectura',
            'id_empleado' => 'nullable|exists:empleados,id_empleado',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'id_empleado' => $request->id_empleado,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        ActivityLog::log('created', "Usuario creado: {$user->name} ({$user->email})", User::class, $user->id);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        $user->load('empleado', 'activityLogs');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $empleados = Empleado::with('cargo')->get();
        return view('users.edit', compact('user', 'empleados'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,tecnico,cajero,lectura',
            'id_empleado' => 'nullable|exists:empleados,id_empleado',
            'is_active' => 'boolean',
        ]);

        $changes = [];
        
        if ($user->name !== $request->name) $changes['name'] = ['old' => $user->name, 'new' => $request->name];
        if ($user->email !== $request->email) $changes['email'] = ['old' => $user->email, 'new' => $request->email];
        if ($user->role !== $request->role) $changes['role'] = ['old' => $user->role, 'new' => $request->role];

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->id_empleado = $request->id_empleado;
        $user->is_active = $request->has('is_active') ? true : false;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $changes['password'] = 'Contraseña actualizada';
        }

        $user->save();

        ActivityLog::log('updated', "Usuario actualizado: {$user->name}", User::class, $user->id, $changes);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // Prevenir que se elimine el último admin
        if ($user->isAdmin() && User::where('role', 'admin')->count() === 1) {
            return redirect()->route('users.index')
                ->with('error', 'No se puede eliminar el único administrador del sistema.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log('deleted', "Usuario eliminado: {$userName}", User::class, $user->id);

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
