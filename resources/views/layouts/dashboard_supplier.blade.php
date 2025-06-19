<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Dashboard | {{ config('app.name', 'Coffee SCMS') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- FontAwesome, Bootstrap, or Supplier-specific CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>
<body>
    <div id="app" class="min-vh-100 bg-light">
        @include('partials.navbar_supplier')

        <div class="container-fluid">
            <div class="row">
                @include('partials.sidebar_supplier')

                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                    @include('partials.flash')
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>