<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/css/foundation.css" />
    <link rel="stylesheet" href="/css/smartmirror.css" />
    <script src="/js/vendor/modernizr.js"></script>
    <script src="/js/vendor/jquery.js"></script>
    <script src="/js/foundation.min.js"></script>
    <script src="/js/foundation/foundation.abide.js"></script>
</head>
<body>
    <div class="row">
        <div class="container">
            @yield('content')
        </div>
    </div>
    <script>
        $(document).foundation();
    </script>
</body>
</html>