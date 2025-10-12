<div class="trip-list">
    @if(empty($trips))
        <p>Нет доступных поездок.</p>
    @else
        @foreach($trips as $trip)
            <div class="trip-item">
                <strong>{{ $trip['start_station']['city'] }} → {{ $trip['end_station']['city'] }}</strong><br>
                <span>{{ \Carbon\Carbon::parse($trip['start_timestamp'])->format('H:i') }}
                    — {{ \Carbon\Carbon::parse($trip['end_timestamp'])->format('H:i') }}</span><br>
                <span>Свободных мест: {{ $trip['available_seats_count'] }}</span>
            </div>
        @endforeach
    @endif
</div>
