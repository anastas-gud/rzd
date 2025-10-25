@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 max-w-xl">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Редактирование профиля</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="bg-white p-6 rounded shadow">
            @csrf

            <div class="mb-4">
                <label for="surname" class="block text-gray-700 font-medium mb-1">Фамилия</label>
                <input type="text" id="surname" name="surname"
                       value="{{ old('surname', $profile['surname']) }}"
                       class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-1">Имя</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $profile['name']) }}"
                       class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4">
                <label for="patronymic" class="block text-gray-700 font-medium mb-1">Отчество</label>
                <input type="text" id="patronymic" name="patronymic"
                       value="{{ old('patronymic', $profile['patronymic']) }}"
                       class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-gray-700 font-medium mb-1">Телефон</label>
                <input type="text" id="phone" name="phone"
                       value="{{ old('phone', $profile['phone']) }}"
                       class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $profile['email']) }}"
                       class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('profile') }}" class="text-gray-600 hover:text-gray-800">← Назад</a>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
@endsection
