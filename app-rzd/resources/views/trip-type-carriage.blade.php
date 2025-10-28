@extends('header-footer')

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $tripCarriageTypes['route']['start_station']['city'] }} →
                        {{ $tripCarriageTypes['route']['end_station']['city'] }}
                    </h1>
                    <p class="text-gray-500 mt-1">
                        Поезд №{{ $tripCarriageTypes['route']['number'] }} — {{ $tripCarriageTypes['train_title'] }}
                    </p>
                </div>

                <div class="text-right mt-4 sm:mt-0">
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($tripCarriageTypes['start_timestamp'])->format('d.m.Y, H:i') }}
                        <span class="text-gray-400">→</span>
                        {{ \Carbon\Carbon::parse($tripCarriageTypes['end_timestamp'])->format('H:i') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($tripCarriageTypes['start_timestamp'])->diffInHours($tripCarriageTypes['end_timestamp']) }}
                        ч
                        {{ \Carbon\Carbon::parse($tripCarriageTypes['start_timestamp'])->diffInMinutes($tripCarriageTypes['end_timestamp']) % 60 }}
                        мин
                    </p>
                </div>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Типы вагонов</h2>

        @if(empty($tripCarriageTypes['carriages_types']))
            <p class="text-gray-500 text-center">Нет доступных мест для покупки.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tripCarriageTypes['carriages_types'] as $type)
                    @livewire('carriage-type-card', [
                        'trip_id' => $tripCarriageTypes['trip_id'],
                        'type_id' => $type['type_id'],
                        'type_title' => $type['type_title'],
                        'carriage_id' => $type['carriage_id'],
                        'seat_number' => $type['seat_number'],
                        'seat_price_min' => $type['seat_price_min'],
                        'seat_price_max' => $type['seat_price_max']
                    ])
                @endforeach
            </div>
        @endif
        </div>
@endsection

