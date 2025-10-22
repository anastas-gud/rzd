@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Регистрация</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium">Логин</label>
                <input type="text" name="login" value="{{ old('login') }}" class="mt-1 w-full border rounded p-2">
                @error('login') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium">Имя</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded p-2">
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Фамилия</label>
                    <input type="text" name="surname" value="{{ old('surname') }}" class="mt-1 w-full border rounded p-2">
                    @error('surname') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-4 mt-2">
                <label class="block text-sm font-medium">Отчество (необязательно)</label>
                <input type="text" name="patronymic" value="{{ old('patronymic') }}" class="mt-1 w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Телефон</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Пароль</label>
                <input type="password" name="password" class="mt-1 w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Подтвердите пароль</label>
                <input type="password" name="password_confirmation" class="mt-1 w-full border rounded p-2">
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Зарегистрироваться
            </button>
        </form>
    </div>
@endsection
