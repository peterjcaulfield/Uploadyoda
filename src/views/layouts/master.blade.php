<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/quasimodal/uploadyoda/css/style.css') }}" />
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/main.js') }}"></script>
</head>
<body>
<div id="nav-back"></div>
<div id="nav">
    <div id="nav-content">
        <ul>
        <li class="active"><a href="/uploadyoda">Home</a></li>
        <li><a href="/uploadyoda/upload">Upload</a></li>
        </ul>
    </div>
</div>
<div id="content">
    <div id="header"></div>
    <div id="body">
        <div id="body-content">
            <h1>Upload</h1>
            <div class="wrap">
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>
