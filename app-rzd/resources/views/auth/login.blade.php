@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Вход</h2>
        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="mb-4">
                <label for="login" class="block text-sm font-medium">Логин</label>
                <input type="text" name="login" id="login" value="{{ old('login') }}" class="mt-1 w-full border rounded p-2">
                @error('login') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium">Пароль</label>
                <input type="password" name="password" id="password" class="mt-1 w-full border rounded p-2">
                @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Войти
            </button>
        </form>
    </div>
@endsection
