<div class="trips-div">
    @if(empty($trips))
        <p class="trips-empty-p">Нет доступных поездок.</p>
    @else
        @foreach($trips as $trip)
            <a href="{{ route('trips', ['trip' => $trip['trip_id']]) }}">
                <div class="trips-trip-block">
                    <p class="trips-small-p">
                        <span class="trips-small-p-bold">Маршрут: {{ $trip['route_number'] }}</span>
                        <span style="color: #8c487c; font-weight: 700;">•</span>
                        Поезд: {{ $trip['train_title'] }}
                        <span style="color: #8c487c; font-weight: 700;">•</span>
                        {{ $trip['start_station']['city'] }}
                        <span style="color: #8c487c; font-weight: 700;">→</span>
                        {{ $trip['end_station']['city'] }}
                    </p>
                    <div class="trips-content-wrapper">
                        <div class="trips-routes">
                            <div class="route-time-block">
                                <p class="trips-small-p">{{ \Carbon\Carbon::parse($trip['start_timestamp'])->locale('ru')->isoFormat('D MMM, dd') }}
                                </p>
                                <p class="trips-routes-p-time">
                                    {{ \Carbon\Carbon::parse($trip['start_timestamp'])->format('H:i') }}</p>
                                <p class="trips-small-p trips-small-p-bold">{{ $trip['start_station']['title'] }}</p>
                            </div>

                            <div class="route-center-block">
                                <div class="arrow-with-circle">
                                    <div class="arrow-circle"></div>
                                    <div class="arrow-line">
                                        <div class="arrow-head"></div>
                                    </div>
                                </div>
                                <p class="trips-small-p">
                                    @if(\Carbon\Carbon::parse($trip['start_timestamp'])->diff(\Carbon\Carbon::parse($trip['end_timestamp']))->d > 0)
                                        {{ \Carbon\Carbon::parse($trip['start_timestamp'])->diff(\Carbon\Carbon::parse($trip['end_timestamp']))->d }}
                                        д
                                    @endif
                                    {{ \Carbon\Carbon::parse($trip['start_timestamp'])->diff(\Carbon\Carbon::parse($trip['end_timestamp']))->h }}
                                    ч
                                    {{ \Carbon\Carbon::parse($trip['start_timestamp'])->diff(\Carbon\Carbon::parse($trip['end_timestamp']))->i }}
                                    мин
                                </p>
                            </div>

                            <div class="route-time-block">
                                <p class="trips-small-p">{{ \Carbon\Carbon::parse($trip['end_timestamp'])->locale('ru')->isoFormat('D MMM, dd') }}
                                </p>
                                <p class="trips-routes-p-time">
                                    {{ \Carbon\Carbon::parse($trip['end_timestamp'])->format('H:i') }}</p>
                                <p class="trips-small-p trips-small-p-bold">{{ $trip['end_station']['title'] }}</p>
                            </div>
                        </div>

                        <div class="trips-seats-block">
                            <p class="trips-seats-p">Осталось мест:
                                <span class="trips-seats-p-bold">{{ $trip['available_seats_count'] }}</span>
                            </p>
                            @foreach($trip['min_price_by_carriage_type'] as $type)
                                <div class="trips-seats trips-seats-p">
                                    <div style="flex: 2;">{{ $type['title'] }}</div>
                                    <div style="flex: 1; text-align: end;" class="trips-seats-p-bold">
                                        от {{ number_format($type['min_price'], 0, '.', ' ') }} ₽
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    @endif
</div>