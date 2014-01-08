<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/quasimodal/uploadyoda/css/style.css') }}" />
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/main.js') }}"></script>
</head>
<body>
<div id="nav-back"></div>
<div id="nav">Nav here</div>
<div id="content">
    <div id="header"></div>
    <div id="body">
        <div id="body-content">
            <div class="wrap">
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>
