{{ Form::open(array('url' => '/uploadyoda/test')) }}
{{ Form::text('upload[name]') }}
{{ Form::text('upload[title]') }}
{{ Form::text('meta') }}
{{ Form::submit('submit') }}
<button>button</button>
test
{{ Form::close() }}
