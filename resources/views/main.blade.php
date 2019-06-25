<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>µSe - SiteMaps</title>
    <link rel="stylesheet"
          href="/css/app.css">
</head>
<body>
<nav class="navbar navbar-dark navbar-expand-lg fixed-top bg-white musee-navbar gradient">
    <div class="container"><a class="navbar-brand logo" href="#">µSée</a>
        <button data-toggle="collapse" class="navbar-toggler" data-target="#navbarNav"><span class="sr-only">Toggle navigation</span><span
                    class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="nav navbar-nav ml-auto">
                <li class="nav-item" role="presentation"><a class="nav-link active" href="{{route('musee.generator')}}">
                        Generator
                    </a>
                </li>
                <li class="nav-item" role="presentation"><a class="nav-link active" href="{{route('musee.config')}}">
                        Config
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main class="page">
    @yield('content')
</main>
<footer class="page-footer">
    <div class="container">
        <span>{{date("Y")}}</span>
    </div>
</footer>
<script type="text/javascript" src="/js/app.js"></script>
</body>
</html>