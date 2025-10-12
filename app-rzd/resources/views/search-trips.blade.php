@extends('header-footer')
@section('content')
<div>    
    @livewire('search-trips-form', [
        'from_city' => $search['fromCity'] ?? '',
        'to_city' => $search['toCity'] ?? '',
        'date' => $search['date'] ?? '',
        'passenger_count' => $search['passengerCount'] ?? 1
    ])
    
    @livewire('trip-list', ['trips' => $trips])
</div>
@endsection