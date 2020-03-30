<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <script src="{{ asset('js/app.js') }}"></script>
  <title>{{ env('APP_NAME') }}</title>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <span class="navbar-brand" href="/">{{ env('APP_NAME') }}</span>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
              <li class="nav-item {{ Request::is('articles*')? 'active' : '' }}">
                <a class="nav-link" href="{{ route('article.index') }}">Articles <span class="sr-only">(current)</span></a>
              </li>
            </ul>
        </div>
    </div>
  </nav>
  <div class="container">
      @yield('content')
  </div>
  <div class="footer container mt-3 pt-3 text-center">
      <div class="text-muted">@Laravel</div>
  </div>
</body>
</html>