@extends('uploadyoda::layouts.master')
@section('content')
        <div id="editContainer">
            <div id="media">
            @include('uploadyoda::pdfjs')
            </div>
        </div>
    <style>
        #editContainer { padding: 20px; max-width: 1024px; margin: 0 auto; }
        #media { padding: 0 0 15px 0; margin-bottom: 20px; height: 1000px; background-color: #404040; }
    </style>
</div>
@stop
@section('footer')
<link rel="stylesheet" href="{{ URL::asset('packages/quasimodal/uploadyoda/pdfjs/viewer.css') }}"></script>
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/pdfjs/compatibility.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/pdfjs/l10n.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/pdfjs/pdf.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/pdfjs/debugger.js') }}"></script>
<script type="text/javascript">var UPLOADYODA_PDF_PATH = "{{'http://' . $_SERVER['HTTP_HOST'] . $path}}";</script>
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/pdfjs/viewer.js') }}"></script>
@stop
