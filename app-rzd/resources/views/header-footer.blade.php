<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>БезПрицепа</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <!-- Шапка -->
    <header>
        <div class="logo-div">
            <a href="{{ route('home') }}">
                <img src="/images/logo.png" alt="ОАО &quot;БезПрицепа&quot;" class="h-10">
            </a>
            <span>БезПрицепа</span>
        </div>

        <div class="login-div">
            @auth
                <!-- Для авторизованных пользователей -->
                <span class="" style="margin: auto;">{{ Auth::user()->name->name . ' ' . Auth::user()->name->surname }}</span>

                <form method="POST" action="{{ route('logout') }}" style="margin: auto;">
                    @csrf
                    <button type="submit" class="login-button">
                        Выход
                    </button>
                </form>
            @else
                <!-- Для гостей -->
                <a href="{{ url('/login') }}" class="login-button">
                    Вход
                </a>
                <a href="{{ url('/register') }}" class="login-button">
                    Регистрация
                </a>
            @endauth
        </div>
    </header>

    <!-- Основной контент -->
    <main>
        @yield('content')
    </main>

    <!-- Подвал -->
    <footer>
        <div class="footer-content">
            <h3>БезПрицепа</h3>
            <div class="footer-contacts-div">
                <div class="footer-contacts-item">
                    <span class="footer-contacts-item-title">+7 (800) 123-45-67</span>
                    <span class="footer-contacts-item-description">звонок бесплатный для всех регионов РФ</span>
                </div>
                <div class="footer-contacts-item">
                    <span class="footer-contacts-item-title">info@without-trailer.ru</span>
                    <span class="footer-contacts-item-description">для вопросов, связанных с электронными
                        билетами</span>
                </div>
                <div class="footer-contacts-item">
                    <span class="footer-contacts-item-title">@without-trailer</span>
                    <span class="footer-contacts-item-description">для вопросов, связанных с электронными
                        билетами</span>
                </div>
            </div>
            <div class="footer-copyright-div">
                <p class="footer-copyright-text">&copy; 2025 ОАО "БезПрицепа". Все права защищены.</p>
            </div>
        </div>
    </footer>
</body>

</html>