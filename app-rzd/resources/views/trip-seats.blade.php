@extends('header-footer')

@section('content')
    <div class="container mx-auto p-6">
        {{-- Заголовок --}}
        <h1 class="text-2xl font-semibold mb-4">
            Поезд {{ $seatsAndCarriages['train_title'] }}
        </h1>

        {{-- Информация о маршруте --}}
        <div class="bg-gray-100 rounded-xl p-4 mb-6">
            <div class="flex flex-col sm:flex-row justify-between">
                <div>
                    <p class="text-lg font-medium">
                        🚉 {{ $seatsAndCarriages['route']['start_station']['city'] }} —
                        {{ $seatsAndCarriages['route']['end_station']['city'] }}
                    </p>
                    <p class="text-gray-600">
                        {{ $seatsAndCarriages['start_timestamp'] }} → {{ $seatsAndCarriages['end_timestamp'] }}
                    </p>
                </div>
                <div class="text-gray-500 mt-2 sm:mt-0">
                    № маршрута: {{ $seatsAndCarriages['route']['number'] }}
                </div>
            </div>
        </div>

        {{-- Блок вагонов --}}
        <h2 class="text-xl font-semibold mb-2">Вагоны (тип:
            {{ $seatsAndCarriages['carriages'][0]['carriage_type']['title'] }})</h2>
        <div class="flex flex-wrap gap-3 mb-6">
            @foreach($seatsAndCarriages['carriages'] as $carriage)
                    <a href="{{ route('trip-seats', [
                    'trip' => $seatsAndCarriages['trip_id'],
                    'carriage_type' => $carriage['carriage_type']['id'],
                    'carriage' => $carriage['carriage_id']
                ]) }}" class="block px-4 py-3 bg-white shadow rounded-xl border hover:border-blue-500 transition">
                        <p class="text-lg font-semibold">🚃 Вагон №{{ $carriage['number'] }}</p>
                        <p class="text-gray-600 text-sm">
                            Свободно мест: <span class="font-medium">{{ $carriage['available_seats_count'] }}</span>
                        </p>
                    </a>
            @endforeach
        </div>

        {{-- Схема мест текущего вагона --}}
        <h2 class="text-xl font-semibold mb-4">Места в вагоне
            №{{ $seatsAndCarriages['seats'][0]['carriage_number'] ?? '-' }}</h2>

        @if(!empty($seatsAndCarriages['seats']))
            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                @foreach($seatsAndCarriages['seats'] as $seat)
                    <div class="p-3 rounded-xl text-center border transition
                                    @if($seat['is_available'])
                                        bg-green-50 border-green-400 hover:bg-green-100 cursor-pointer
                                    @else
                                        bg-red-50 border-red-400 opacity-60 cursor-not-allowed
                                    @endif
                                ">
                        <p class="font-semibold text-gray-800">Место {{ $seat['number'] }}</p>
                        <p class="text-sm text-gray-500">{{ $seat['price'] }} ₽</p>
                        @if(!$seat['is_available'])
                            <p class="text-xs text-red-500 font-medium">занято</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 italic">Места не найдены.</p>
        @endif
    </div>
@endsection