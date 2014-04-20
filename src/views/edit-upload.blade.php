@extends('uploadyoda::layouts.master')

@section('content')
<div id="editContainer">
    {{ Form::open(array('url' => '/uploadyoda/' . $upload->id . '/update', 'class' => 'form editForm', 'id' => 'editForm')) }}
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
            {{ Form::submit('Update', array('class'=>'btn btn-large btn-primary', 'id' => 'updateButton'))}}
            </div>
            <div id="editDelete">
            {{ Form::submit('Delete', array('class'=>'btn btn-large btn-danger', 'id' => 'deleteButton'))}}
            </div>
            <div style="clear: both;"></div>
            <div id="updateStatus">
            </div>
        </div>
    </div>
    <div id="editForm">
        {{ Form::text('meta[title]', $upload->metable->title, array('class'=>'form-control', 'placeholder'=>'title')) }}
        <label for="description">Description</label>
        {{ Form::text('meta[description]', $upload->metable->description, array('class'=>'form-control', 'placeholder'=>'description')) }}
        <label for="caption">Caption</label>
        {{ Form::text('meta[caption]', $upload->metable->caption, array('class'=>'form-control', 'placeholder'=>'caption')) }}
    </div>
{{ Form::close() }}
