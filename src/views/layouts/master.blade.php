<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/quasimodal/uploadyoda/css/style.css') }}" />
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/main.js') }}"></script>
</head>
<body>
    <div id="header">
        <p id="logo-text">Uploadyoda</p>
    </div>
    <div id="container">
        <div id="nav">
        </div>
        <div id="content">
            <div id="content-inner">
                @yield('content')
            <div>
        </div>
    </div>
</body>
</html>
