@extends('uploadyoda::layouts.master')

@section('content')
<div id="editContainer">
    {{ Form::open(array('class' => 'form editForm')) }}
    <div id="publishArea">
        <div id="meta">
            <div class="metadata">Uploaded: <span class="bold">{{ $upload->created_at }}</span></div>
            <div class="metadata">Filename:<br/> <span class="bold">{{ $upload->name }}</span></div>
            <div class="metadata">Filetype: <span class="bold">{{ $upload->mime_type }}</span></div>
            <div class="metadata">Filesize: <span class="bold">{{ $upload->size }}</span></div>
            <div class="metadata">File Url:<br/> <input id="fileUrl" type="text" value="{{ $_SERVER['HTTP_HOST'] . $path }}" readonly></div>
        </div>
        <div id="editActions">
            <div id="editUpdate">
            {{ Form::submit('Update', array('class'=>'btn btn-large btn-primary'))}}
            </div>
            <div id="editDelete">
            {{ Form::submit('Delete', array('class'=>'btn btn-large btn-danger'))}}
            </div>
        </div>
    </div>
    <div id="editForm">
        {{ Form::text('title', null, array('class'=>'form-control', 'placeholder'=>'title')) }}
        <div id="media">
            <img src="{{$path}}"/>
        </div>
        <label for="alt-text">Alternative text</label>
        {{ Form::text('alt-text', null, array('class'=>'form-control', 'placeholder'=>'alt text')) }}
        <label for="description">Description</label>
        {{ Form::text('description', null, array('class'=>'form-control', 'placeholder'=>'description')) }}
        <label for="caption">Caption</label>
        {{ Form::text('caption', null, array('class'=>'form-control', 'placeholder'=>'caption')) }}
    </div>
    {{ Form::close() }}
    <style>
        #editContainer { padding: 20px; }
        #editForm { overflow: hidden;  }
        #publishArea { float: right; width: 300px; margin: 0 0 0 20px; border: 1px solid #e5e5e5; -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04); box-shadow: 0 1px 1px rgba(0,0,0,.04); }
        #meta { background-color: #fff; padding: 10px; border-bottom: 1px solid #ddd;  }
        #editActions { padding: 10px; overflow: auto; background: #f5f5f5; }
        #fileUrl { width: 100%; margin: 5px 0 0 0; }
        #media { padding: 25px 0 25px 0; }
        #media img { width: auto; }
        #editUpdate { float: right; display: inline-block; }
        #editDelete { float: left; display: inline-block; }
        .bold { font-weight: bold; }
        .metadata { padding: 5px 0 5px 0; }
    </style>
</div>
@stop
