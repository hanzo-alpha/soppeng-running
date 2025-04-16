<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content="LATBIMTEK Jaga Desa SULBAR "/>
    <meta name="keywords" content="pelatihan, bimtek, jaga desa, sulbar, latbimtek, diklat"/>
    <meta name="author" content="CV. Dianra"/>

    <!-- Site Title -->
    <title>{{ $title ?? config('app.name') }}</title>
    <!-- Site favicon -->
    <link rel="shortcut icon" href="{{ asset('frontend/running/favicon.png') }}"/>

    <!-- Swiper js -->
    <link rel="stylesheet" href="{{ asset('frontend/css/swiper-bundle.min.css') }}" type="text/css"/>

    <!--Material Icon -->
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/css/materialdesignicons.min.css') }}"/>

    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/css/style.css') }}"/>
    @livewireStyles
</head>

<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="60">
<!--Navbar Start-->
<nav class="navbar navbar-expand-lg fixed-top navbar-custom sticky-dark" id="navbar-sticky">
    <div class="container">
        <!-- LOGO -->
        <a class="logo text-uppercase" href="/">
            <img height="60px" src="{{ asset('frontend/running/Logo_8.png') }}"
                 alt=""/>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="mdi mdi-menu"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mx-auto navbar-center" id="mySidenav">
                <li class="nav-item">
                    {{--                    <a href="{{ route('home') }}" class="nav-link">Home</a>--}}
                    <a href="#home" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    {{--                    <a href="{{ route('filament.app.pages.pendaftaran') }}" class="nav-link">Pendaftaran Otomatis (Virtual Account)</a>--}}
                    <a href="#panduan" class="nav-link">Panduan</a>
                </li>
{{--                <li class="nav-item">--}}
{{--                    --}}{{--                    <a href="{{ route('filament.app.pages.pendaftaran-manual') }}" class="nav-link">Pendaftaran Manual--}}
{{--                    --}}{{--                        (Upload Bukti Pembayaran)</a>--}}
{{--                    <a href="#testimonial" class="nav-link">Pendukung Kegiatan</a>--}}
{{--                </li>--}}
            </ul>
            {{--            <ul class="navbar-nav navbar-center">--}}
            {{--                <li class="nav-item">--}}
            {{--                    <a href="{{ route('filament.admin.auth.login') }}" class="btn btn-sm nav-btn">Masuk</a>--}}
            {{--                </li>--}}
            {{--            </ul>--}}
        </div>
    </div>
</nav>
<!-- Navbar End -->

<!-- home-agency start -->
{{ $slot }}
<!-- home-agency end -->

<!-- Back to top -->
<a href="#" onclick="topFunction()" class="back-to-top-btn btn btn-gradient-primary" id="back-to-top"><i
        class="mdi mdi-chevron-up"></i></a>
@livewireScriptConfig
<!-- javascript -->
<script src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>
<!-- counter -->
{{--<script src="frontend/js/counter.init.js"></script>--}}
<!-- swiper -->
<script src="{{ asset('frontend/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('frontend/js/swiper.js') }}"></script>
<script src="{{ asset('frontend/js/app.js') }}"></script>
</body>
</html>
