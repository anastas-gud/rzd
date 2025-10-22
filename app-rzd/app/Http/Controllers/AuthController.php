<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Name;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string', 'unique:users,login', 'min:3', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],

            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'patronymic' => ['nullable', 'string', 'max:255'],

            'phone' => ['required', 'string', 'unique:contacts,phone'],
            'email' => ['required', 'email', 'unique:contacts,email'],
        ]);

        $name = Name::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'patronymic' => $validated['patronymic'] ?? null,
        ]);

        $contact = Contact::create([
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
        ]);

        $role = Role::where('title', 'USER')->first();

        $user = User::create([
            'login' => $validated['login'],
            'password' => $validated['password'],
            'role_id' => $role->id,
            'contact_id' => $contact->id,
            'name_id' => $name->id,
        ]);

        Auth::login($user);

        return redirect('/');
    }
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
        return back()->withErrors([
            'login' => 'Неверный логин или пароль.',
        ]);

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
