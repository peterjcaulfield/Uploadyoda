@extends('uploadyoda::layouts.welcome')

@section('content')
{{ Form::open(array('url'=>'uploadyoda_user/store', 'id'=>'form-signup')) }}
    <h2 class="form-signup-heading">Welcome to Uploadyoda</h2>
    <p>You are seeing this welcome screen as you are running uploadyoda's default route filter.
    To use your own filter with uploadyoda, just change the key/value in the packages config:</p>
    <p><code>'routeFilter' => 'your route filter'</code></p>
    <p>If you intend on using the default filter, please register a user via the form below</p>
 
    <div class="formInput">
        {{ Form::text('firstname', null, array('class'=>'form-control', 'placeholder'=>'First Name')) }}
        <span class="inputError"><?php echo $errors->first('firstname'); ?></span>
    </div>
    <div class="formInput">
        {{ Form::text('lastname', null, array('class'=>'form-control', 'placeholder'=>'Last Name')) }}
        <span class="inputError"><?php echo $errors->first('lastname'); ?></span>
    </div>
    <div class="formInput">
        {{ Form::text('email', null, array('class'=>'form-control', 'placeholder'=>'Email Address')) }}
        <span class="inputError"><?php echo $errors->first('email'); ?></span>
    </div>
    <div class="formInput">
        {{ Form::password('password', array('class'=>'form-control', 'placeholder'=>'Password')) }}
        <span class="inputError"><?php echo $errors->first('password'); ?></span>
    </div>
    <div class="formInput">
        {{ Form::password('password_confirmation', array('class'=>'form-control', 'placeholder'=>'Confirm Password')) }}
        <span class="inputError"><?php echo $errors->first('password_confirmation'); ?></span>
    </div>
    {{ Form::submit('Register', array('class'=>'btn btn-large btn-primary btn-block'))}}
{{ Form::close() }}
@stop
