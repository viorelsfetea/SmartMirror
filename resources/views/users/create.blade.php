<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
</head>
<body>
    <form action="" method="POST">
        Name: <input type="text" name="name" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="device_id" value="{{ $device_id }}">
        <input type="hidden" name="weight" value="{{ $weight }}">
        <input type="submit" value="Submit">
    </form>
</body>
</html>