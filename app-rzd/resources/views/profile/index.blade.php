@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Личный кабинет</h1>

        <div class="bg-white p-4 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-2">Профиль</h2>
            <p><strong>Логин:</strong> {{ $profile['login'] }}</p>
            <p><strong>Имя:</strong> {{ $profile['surname'] }} {{ $profile['name'] }} {{ $profile['patronymic'] }}</p>
            <p><strong>Телефон:</strong> {{ $profile['phone'] }}</p>
            <p><strong>Email:</strong> {{ $profile['email'] }}</p>
            <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">Редактировать</a>
        </div>

        <div class="bg-white p-4 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-2">Ваши билеты</h2>

            <h3 class="text-lg font-bold mt-4">Активные</h3>
            @foreach ($tickets['active'] as $ticket)
                <div class="border-b py-2">
                    <p><strong>Маршрут:</strong> {{ $ticket->trip->route->number }}</p>
                    <p><strong>Поезд:</strong> {{ $ticket->trip->train->title }}</p>
                    <p><strong>Отправление:</strong> {{ $ticket->trip->start_timestamp }}</p>
                    <p><strong>Цена:</strong> {{ $ticket->final_price }} ₽</p>
                    <form method="POST" action="{{ route('profile.ticket.cancel', $ticket->id) }}">
                        @csrf
                        <button type="submit" class="text-red-500 hover:underline">Отменить</button>
                    </form>
                </div>
            @endforeach

            <h3 class="text-lg font-bold mt-4">Прошедшие</h3>
            @foreach ($tickets['past'] as $ticket)
                <div class="border-b py-2 text-gray-500">
                    <p><strong>Маршрут:</strong> {{ $ticket->trip->route->number }}</p>
                    <p><strong>Отправление:</strong> {{ $ticket->trip->start_timestamp }}</p>
                    <p><strong>Цена:</strong> {{ $ticket->final_price }} ₽</p>
                </div>
            @endforeach

            <h3 class="text-lg font-bold mt-4">Отменённые</h3>
            @foreach ($tickets['canceled'] as $ticket)
                <div class="border-b py-2 text-gray-400 italic">
                    <p><strong>Маршрут:</strong> {{ $ticket->trip->route->number }}</p>
                    <p><strong>Отменён:</strong> {{ $ticket->updated_at }}</p>
                </div>
            @endforeach
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-3">Активные брони</h2>

            <div id="bookings-list">
                @forelse($bookings as $booking)
                    @php
                        // Преобразуем дату в timestamp на сервере
                        try {
                            $expiresAt = \Carbon\Carbon::parse($booking['expires_at']);
                            $timestamp = $expiresAt->getTimestamp() * 1000; // в миллисекундах
                        } catch (Exception $e) {
                            $timestamp = 0;
                        }
                    @endphp
                    <div
                        class="booking-item border rounded p-4 mb-3 bg-white shadow-sm"
                        id="booking-{{ $booking['booking_id'] }}"
                        data-expires-at="{{ $timestamp }}"
                    >
                        <p><strong>Маршрут:</strong> {{ $booking['trip']['start_station'] }} — {{ $booking['trip']['end_station'] }}</p>
                        <p><strong>Дата:</strong> {{ $booking['trip']['start_time'] }}</p>
                        <p><strong>Мест:</strong> {{ $booking['seats_count'] }}</p>
                        <p><strong>Цена:</strong> {{ $booking['total_price'] }} ₽</p>

                        <p class="text-gray-700">
                            <strong>Истекает через:</strong>
                            <span class="timer">—</span>
                        </p>

                        <a href="{{ route('home', ['booking' => $booking['booking_id']]) }}"
                           class="inline-block bg-blue-600 text-white px-4 py-2 rounded mt-2 hover:bg-blue-700">
                            Продолжить оформление
                        </a>
                    </div>
                @empty
                    <p class="text-gray-600">Нет активных броней</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function startBookingTimers() {
            const bookingItems = document.querySelectorAll('.booking-item');

            if (bookingItems.length === 0) {
                return;
            }

            // Выведем подробную информацию о каждой брони
            bookingItems.forEach((item, index) => {
                const expiresAt = item.getAttribute('data-expires-at');
                const expiresAtNum = parseInt(expiresAt);
                const now = Date.now();
                const diff = expiresAtNum - now;

                console.log(`Booking ${index + 1}:`, {
                    id: item.id,
                    expiresAtRaw: expiresAt,
                    expiresAtNum: expiresAtNum,
                    now: now,
                    difference: diff,
                    isValid: !isNaN(expiresAtNum) && expiresAtNum > 0
                });
            });

            function updateTimers() {
                const now = Date.now();
                let hasActiveBookings = false;

                bookingItems.forEach(item => {
                    if (!item.parentNode) return;

                    const expiresAt = parseInt(item.getAttribute('data-expires-at'));
                    const timerEl = item.querySelector('.timer');

                    if (!timerEl || isNaN(expiresAt) || expiresAt <= 0) {
                        timerEl.textContent = 'ошибка';
                        return;
                    }

                    const diff = expiresAt - now;

                    if (diff <= 0) {
                        // Время истекло
                        timerEl.textContent = '00:00';
                        timerEl.style.color = 'red';
                        timerEl.style.fontWeight = 'bold';
                    } else {
                        hasActiveBookings = true;
                        const minutes = Math.floor(diff / 60000);
                        const seconds = Math.floor((diff % 60000) / 1000);
                        timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                        // Меняем цвет в зависимости от оставшегося времени
                        if (minutes < 1) {
                            timerEl.style.color = 'red';
                            timerEl.style.fontWeight = 'bold';
                        } else if (minutes < 5) {
                            timerEl.style.color = 'orange';
                            timerEl.style.fontWeight = 'bold';
                        } else {
                            timerEl.style.color = '';
                            timerEl.style.fontWeight = '';
                        }
                    }
                });

                // Если все брони истекли, обновим страницу через 5 секунд
                if (!hasActiveBookings) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 5000);
                }
            }

            // Запускаем сразу
            updateTimers();
            // И каждую секунду
            setInterval(updateTimers, 1000);
        }

        // Запускаем когда страница загружена
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startBookingTimers);
        } else {
            startBookingTimers();
        }
    </script>
@endsection
