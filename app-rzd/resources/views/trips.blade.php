@extends('header-footer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Прогресс-бар -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-2">
            @foreach(['wagon-type', 'seats', 'auth', 'passenger', 'payment'] as $step)
                <div class="text-center flex-1">
                    <div class="w-8 h-8 rounded-full {{ $currentStep >= $loop->iteration ? 'bg-blue-600 text-white' : 'bg-gray-300' }} mx-auto flex items-center justify-center">
                        {{ $loop->iteration }}
                    </div>
                    <p class="text-sm mt-1 {{ $currentStep >= $loop->iteration ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
                        @if($loop->iteration == 1) Тип вагона
                        @elseif($loop->iteration == 2) Места
                        @elseif($loop->iteration == 3) Вход
                        @elseif($loop->iteration == 4) Пассажиры
                        @elseif($loop->iteration == 5) Оплата
                        @endif
                    </p>
                </div>
                @if(!$loop->last)
                    <div class="flex-1 h-1 bg-gray-300 {{ $currentStep > $loop->iteration ? 'bg-blue-600' : '' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Контент страницы -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @if($currentStep == 1)
            <!-- Выбор типа вагона -->
            <h2 class="text-2xl font-bold mb-6">Выберите тип вагона</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($wagonTypes as $type)
                    <label class="cursor-pointer">
                        <input type="radio" name="wagon_type" value="{{ $type->id }}" 
                               class="hidden peer" wire:model="selectedWagonType">
                        <div class="border-2 rounded-lg p-6 peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-blue-400 transition-colors">
                            <h3 class="text-xl font-semibold mb-2">{{ $type->name }}</h3>
                            <p class="text-lg font-bold text-blue-600 mb-2">{{ $type->price }} ₽</p>
                            <p class="text-gray-600">Осталось мест: {{ $type->available_seats }}</p>
                        </div>
                    </label>
                @endforeach
            </div>

        @elseif($currentStep == 2)
            <!-- Выбор мест -->
            <h2 class="text-2xl font-bold mb-6">Выберите места</h2>
            
            <!-- Выбор вагона -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Выберите вагон:</label>
                <select wire:model="selectedWagon" class="border rounded-lg px-4 py-2 w-full md:w-auto">
                    @foreach($availableWagons as $wagon)
                        <option value="{{ $wagon->id }}">Вагон {{ $wagon->number }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Схема вагона -->
            <div class="bg-gray-100 p-6 rounded-lg mb-6">
                <div class="grid grid-cols-4 gap-4">
                    @foreach($seats as $seat)
                        <button wire:click="toggleSeat({{ $seat->id }})"
                                class="seat p-4 rounded border-2 {{ $seat->is_available ? 'bg-white border-green-500 hover:bg-green-50' : 'bg-gray-300 border-gray-400 cursor-not-allowed' }} {{ in_array($seat->id, $selectedSeats) ? 'bg-blue-500 text-white border-blue-700' : '' }}"
                                {{ !$seat->is_available ? 'disabled' : '' }}>
                            Место {{ $seat->number }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Итоговая информация -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-lg">Выбрано мест: <span class="font-semibold">{{ count($selectedSeats) }}</span></p>
                <p class="text-xl font-bold">Общая стоимость: {{ $totalPrice }} ₽</p>
            </div>

        @elseif($currentStep == 3)
            <!-- Авторизация/регистрация -->
            @if(auth()->check())
                <div class="text-center py-8">
                    <p class="text-xl">Вы вошли как {{ auth()->user()->name }}</p>
                </div>
            @else
                @livewire('auth-modal', ['redirectToBooking' => true])
            @endif

        @elseif($currentStep == 4)
            <!-- Данные пассажира -->
            <h2 class="text-2xl font-bold mb-6">Данные пассажира</h2>
            <form wire:submit.prevent="savePassengerData">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Фамилия</label>
                        <input type="text" wire:model="passengerData.last_name" 
                               class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Имя</label>
                        <input type="text" wire:model="passengerData.first_name" 
                               class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Отчество</label>
                        <input type="text" wire:model="passengerData.middle_name" 
                               class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Дата рождения</label>
                        <input type="date" wire:model="passengerData.birth_date" 
                               class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" wire:model="passengerData.email" 
                               class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                        <input type="tel" wire:model="passengerData.phone" 
                               class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </form>

        @elseif($currentStep == 5)
            <!-- Оплата -->
            <h2 class="text-2xl font-bold mb-6">Оплата</h2>
            <div class="max-w-md mx-auto">
                <form wire:submit.prevent="processPayment">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Номер карты</label>
                        <input type="text" placeholder="0000 0000 0000 0000" 
                               class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Срок действия</label>
                            <input type="text" placeholder="ММ/ГГ" 
                                   class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                            <input type="text" placeholder="123" 
                                   class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Имя владельца</label>
                        <input type="text" placeholder="IVAN IVANOV" 
                               class="w-full border rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <p class="text-lg font-semibold">К оплате: {{ $totalPrice }} ₽</p>
                    </div>
                </form>
            </div>
        @endif

        <!-- Навигационные кнопки -->
        <div class="flex justify-between mt-8">
            @if($currentStep > 1)
                <button wire:click="previousStep" 
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    Назад
                </button>
            @else
                <div></div>
            @endif

            @if($currentStep < 5)
                <button wire:click="nextStep" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    ПРОДОЛЖИТЬ
                </button>
            @else
                <button wire:click="completePayment" 
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    ОПЛАТИТЬ
                </button>
            @endif
        </div>
    </div>
</div>
@endsection
