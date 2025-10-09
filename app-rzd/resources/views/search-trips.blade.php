@extends('header-footer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Поисковая форма -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        @livewire('search-form', [
            'from' => $searchData['from'] ?? '',
            'to' => $searchData['to'] ?? '',
            'date' => $searchData['date'] ?? '',
            'passengers' => $searchData['passengers'] ?? 1
        ])
    </div>

    <!-- Список маршрутов -->
    <h2 class="text-3xl font-bold mb-6">Найденные маршруты</h2>
    
    @if($routes->count() > 0)
        <div class="space-y-6">
            @foreach($routes as $route)
                <div class="route-card bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
                     onclick="window.location='{{ route('route.show', $route->id) }}'">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <!-- Основная информация -->
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-blue-600">{{ $route->train->name }}</h3>
                            <div class="flex items-center mt-2">
                                <div class="text-center">
                                    <p class="font-semibold">{{ $route->departure_station->name }}</p>
                                    <p class="text-gray-600">{{ $route->departure_time->format('H:i') }}</p>
                                </div>
                                <div class="mx-4 flex-1 border-t-2 border-dashed border-gray-300"></div>
                                <div class="text-center">
                                    <p class="font-semibold">{{ $route->arrival_station->name }}</p>
                                    <p class="text-gray-600">{{ $route->arrival_time->format('H:i') }}</p>
                                </div>
                            </div>
                            <p class="text-gray-600 mt-2">В пути: {{ $route->duration }}</p>
                        </div>

                        <!-- Свободные места -->
                        <div class="mt-4 md:mt-0 md:ml-6">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="bg-blue-100 p-3 rounded">
                                    <p class="font-semibold">Купе</p>
                                    <p class="text-lg">{{ $route->available_coupe }}</p>
                                </div>
                                <div class="bg-green-100 p-3 rounded">
                                    <p class="font-semibold">Плацкарт</p>
                                    <p class="text-lg">{{ $route->available_platskart }}</p>
                                </div>
                                <div class="bg-purple-100 p-3 rounded">
                                    <p class="font-semibold">СВ</p>
                                    <p class="text-lg">{{ $route->available_sv }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-xl text-gray-600">По вашему запросу маршруты не найдены</p>
        </div>
    @endif
</div>
@endsection
