<div class="flex flex-col items-center">
    <div class="w-full max-w-5xl overflow-auto border rounded shadow"
        style="">
        {{-- background-image: url({{ "../images/"+$carriageTypeId+".jpg" }}); --}}
        <svg viewBox="0 0 1050 200" width="100%">
            @foreach($layout as $seatLayout)
                @php
                    $seat = collect($seats)->firstWhere('number', $seatLayout['number']);
                    if (!$seat)
                        continue;
                    $seatId = $seat['seat_id'];
                    $seatNumber = $seat['number'];
                    $isAvailable = $seat['is_available'];
                    $isSelected = in_array($seatId, $selectedSeatIds);
                    $fill = !$isAvailable ? '#d1d5db' : ($isSelected ? '#4caf50' : '#ffffff');
                    $cursor = !$isAvailable ? 'not-allowed' : 'pointer';
                @endphp

                <rect x="{{ $seatLayout['x'] }}" y="{{ $seatLayout['y'] }}" width="{{ $seatLayout['w'] }}"
                    height="{{ $seatLayout['h'] }}" rx="6" ry="6" stroke="#333" fill="{{ $fill }}"
                    style="cursor: {{ $cursor }}" wire:click="toggleSeat({{ $seatId }})">
                    @if (isset($seatLayout['title']))
                        <title>
                            Место №{{ $seatNumber }}&#10;{{ $seatLayout['title'] }}&#10;{{ $seat['price'] }} ₽
                        </title>
                    @else
                        <title>
                            Место №{{ $seatNumber }}&#10;{{ $seat['price'] }} ₽
                        </title>
                    @endif
                </rect>
            @endforeach
        </svg>
    </div>

    <div class="mt-4">
        <p class="text-sm">
            Выбрано: {{ count($selectedSeatIds) }} / 10
        </p>

        <div class="flex flex-wrap gap-2 mt-2">
            @foreach($selectedSeatIds as $id)
                @php
                    $s = collect($seats)->firstWhere('seat_id', $id);
                @endphp
                @if($s)
                    <span class="">{{ $s['number'] }}</span>
                @endif
            @endforeach
        </div>
    </div>

    <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded disabled:bg-gray-400"
        wire:click="$emitUp('seatsSelected', {{ json_encode($selectedSeatIds) }})" @disabled(empty($selectedSeatIds))>
        Продолжить
    </button>
</div>