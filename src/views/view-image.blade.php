@extends('uploadyoda::layouts.master')
@section('content')
        <div id="editContainer">
            <div id="mediaContainer">
                <img id="media" src="{{$path}}"/>
                {{$upload->name}}
            <div>
        </div>
    <style>
        #editContainer { padding: 20px; }
        #mediaContainer { text-align: center; }
        #media { padding: 0 0 15px 0; margin: 0 auto 20px auto; width: auto; max-width:100%; display: block; }
    </style>
</div>
@stop
@section('footer')
@stop
