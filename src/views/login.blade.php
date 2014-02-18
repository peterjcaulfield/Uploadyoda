@extends('uploadyoda::layouts.welcome')

@section('content')
@if(Session::has('danger'))
    <div class="alert alert-danger form-alert">{{ Session::get('danger') }}</div>
@endif
@if(Session::has('success'))
    <div class="alert alert-success form-alert">{{ Session::get('success') }}</div>
@endif
{{ Form::open(array('url'=>'uploadyoda_user/login', 'id' => 'form-login')) }}
    <h2 class="form-signin-heading">Uploadyoda Login</h2>
 
    <p>Don't have an account already? Click <a href="/uploadyoda_user/welcome">here</a> to register.</p>
    <div class="formInput">
        {{ Form::text('email', null, array('class'=>'form-control', 'placeholder'=>'Email Address')) }}
        <span class="inputError"><?php echo $errors->first('email'); ?></span>
    </div>
    <div class="formInput">
        {{ Form::password('password', array('class'=>'form-control', 'placeholder'=>'Password')) }}
        <span class="inputError"><?php echo $errors->first('password'); ?></span>
    </div>
        {{ Form::submit('Login', array('class'=>'btn btn-large btn-primary btn-block'))}}

{{ Form::close() }}
@stop
