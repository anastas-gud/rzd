<div class="flex flex-col items-center">
    <h2 class="text-lg font-semibold mb-2">Выбор мест в вагоне №{{ $carriageId }}</h2>
    <div class="w-full max-w-5xl overflow-auto border rounded shadow" style="max-height: 400px;">
        <svg viewBox="0 0 600 200" width="100%" class="border rounded shadow">
            @foreach($layout as $seatLayout)
                @php
                    $seat = collect($seats)->firstWhere('number', $seatLayout['id']);
                    if (!$seat)
                        continue;

                    $seatId = $seat['seat_id'];
                    $isAvailable = $seat['is_available'];
                    $isSelected = in_array($seatId, $selectedSeatIds);
                    $fill = !$isAvailable ? '#d1d5db' : ($isSelected ? '#4caf50' : '#ffffff');
                    $cursor = !$isAvailable ? 'not-allowed' : 'pointer';
                @endphp

                <rect x="{{ $seatLayout['x'] }}" y="{{ $seatLayout['y'] }}" width="15" height="15" rx="6" ry="6"
                    stroke="#333" fill="{{ $fill }}" style="cursor: {{ $cursor }}" wire:click="toggleSeat({{ $seatId }})" />

                <text x="{{ $seatLayout['x'] + 7 }}" y="{{ $seatLayout['y'] + 10 }}" text-anchor="middle" font-size="8"
                    fill="#000">
                    {{ $seatLayout['id'] }}
                </text>
            @endforeach
        </svg>
    </div>

    <div class="mt-4">
        <p class="text-sm">
            Выбрано: <strong>{{ count($selectedSeatIds) }}</strong> / 10
        </p>

        <div class="flex flex-wrap gap-2 mt-2">
            @foreach($selectedSeatIds as $id)
                @php
                    $s = collect($seats)->firstWhere('seat_id', $id);
                @endphp
                @if($s)
                    <span class="px-2 py-1 bg-green-200 rounded">{{ $s['number'] }}</span>
                @endif
            @endforeach
        </div>
    </div>

    <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded disabled:bg-gray-400"
        wire:click="$emitUp('seatsSelected', {{ json_encode($selectedSeatIds) }})" @disabled(empty($selectedSeatIds))>
        Продолжить
    </button>
</div>