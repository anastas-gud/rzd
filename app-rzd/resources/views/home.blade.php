@extends('header-footer')

@section('content')
    <section>
        <div class="home-bar">
            <div class="home-bar-content">
                <h1><span>КУПИТЬ БИЛЕТ</span></h1>
                @livewire('search-form')
            </div>
        </div>
    </section>

    <section>
        <div class="block-content-container">
            <div class="block-content-grid">
                <livewire:block-content : href="/" icon="🎫" title="Мои билеты"
                    description="Личный кабинет пассажира. Просмотр и управление билетами.">
                <livewire:block-content : href="/" icon="🚆" title="Поезда и маршруты"
                    description="Категории поездов, типы вагонов, услуги в поезде.">
                <livewire:block-content : href="/" icon="🏛️" title="Вокзалы"
                    description="Информация о железнодорожных вокзалах.">
            </div>
        </div>
    </section>
@endsection