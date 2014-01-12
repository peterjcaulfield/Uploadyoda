<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/quasimodal/uploadyoda/css/style.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/quasimodal/uploadyoda/libs/font-awesome-4.0.3/css/font-awesome.min.css') }}" />
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/main.js') }}"></script>
</head>
<body>
<div id="nav-back"></div>
<div id="nav">
    <div id="nav-content">
        <ul>
        <li class="{{ Request::is('uploadyoda') ? 'active' : ''}}"><a href="/uploadyoda"><i class="fa fa-home fa-fw"></i>&nbsp;&nbsp;&nbsp;Home</a></li>
        <li class="{{ Request::is('uploadyoda/upload') ? 'active' : ''}}"><a href="/uploadyoda/upload"><i class="fa fa-upload fa-fw"></i>&nbsp;&nbsp;&nbsp;Upload</a></li>
        </ul>
    </div>
</div>
<div id="content">
    <div id="header"></div>
    <div id="body">
        <div id="body-content">
        <h1><?php echo $pageTitle; ?></h1>
            <div class="wrap">
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>
