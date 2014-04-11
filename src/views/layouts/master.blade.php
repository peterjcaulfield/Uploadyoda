<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/quasimodal/uploadyoda/css/bootstrap.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/quasimodal/uploadyoda/css/style.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ URL::asset('packages/quasimodal/uploadyoda/libs/font-awesome-4.0.3/css/font-awesome.min.css') }}" />
<script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/main.js') }}"></script>
</head>
<body>
<div id="content">
    <div id="header">
        <ul class="nav nav-pills">
            <li class="{{ Request::is('uploadyoda') ? 'active' : ''}}"><a href="<?php echo URL::route('uploadyodaHome'); ?>"><i class="fa fa-home fa-fw"></i>&nbsp;&nbsp;&nbsp;Home</a></li>
            <li class="{{ Request::is('uploadyoda/upload') ? 'active' : ''}}"><a href="<?php echo URL::route('uploadyodaUpload'); ?>"><i class="fa fa-upload fa-fw"></i>&nbsp;&nbsp;&nbsp;Upload</a></li>
        </ul>
        <span id="logout">
            <a href="/uploadyoda_user/logout">Logout</a>
        </span>
    </div>
    <div id="alertContainer">
        <div id="alert"></div>
    </div>
    <div class="wrap">
    @yield('content')
    </div>
</div>
@yield('footer')
</body>
</html>
