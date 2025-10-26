<div class="border border-gray-200 bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition">
    <div class="flex justify-between items-start">
        <h3 class="text-lg font-semibold text-gray-900">{{ $type_title }}</h3>
    </div>

    <div class="mt-3 text-gray-700">
        <p>
            Мест доступно: <span class="font-semibold">{{ $seat_number }}</span>
        </p>
        <p class="mt-1">
            Цена:
            <span class="text-red-600 font-semibold">{{ $seat_price_min }}</span>
            –
            <span class="text-red-600 font-semibold">{{ $seat_price_max }}</span> ₽
        </p>
    </div>

    <div class="mt-4">
        <a wire:click.prevent="chooseSeats"
           class="inline-block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition cursor-pointer">
            Выбрать места
        </a>
    </div>
</div>
