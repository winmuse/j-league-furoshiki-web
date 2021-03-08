@extends('layouts.admin')

@section('title', 'Dropbox編集｜Dropbox管理')

@section('content_header_label')
    <h1>Dropbox編集</h1>
@stop

@php
/** @var App\Models\DropboxAccount $account */
@endphp

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.dropbox.update', 'method' => 'PUT', 'id' => 'test-form']) !!}
    <div class="card">
        <div class="card-body media-edit">
            <div class="row">
                <!-- <div class="col-md-12">
                    <div class="form-group">
                        <label>App Key</label>
                        {{ Form::text('dropbox[app_key]', $account->app_key, 
                        ['class' => 'form-control' . ($errors->has('dropbox.app_key') ? ' is-invalid' : ''), 'id' => 'dropbox-app-key']) }}
                        @if ($errors->has('dropbox.app_key'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('dropbox.app_key') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>App Secret</label>
                        {{ Form::text('dropbox[app_secret]', $account->app_secret, 
                        ['class' => 'form-control' . ($errors->has('dropbox.app_secret') ? ' is-invalid' : ''), 'id' => 'dropbox-app-secret']) }}
                        @if ($errors->has('dropbox.app_secret'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('dropbox.app_secret') }}</strong>
                        </span>
                        @endif
                    </div>
                </div> -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Access Token</label>
                        {{ Form::text('dropbox[_token]', old('dropbox._token') ? old('dropbox._token') : $account->_token, 
                        ['class' => 'form-control' . ($errors->has('dropbox._token') ? ' is-invalid' : ''), 'id' => 'dropbox-app-secret']) }}
                        @if ($errors->has('dropbox._token'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('dropbox._token') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Folder</label>
                        {{ Form::text('dropbox[folder]', old('dropbox.folder') ? old('dropbox.folder') : $account->folder, 
                        ['class' => 'form-control' . ($errors->has('dropbox.folder') ? ' is-invalid' : ''), 'id' => 'dropbox-app-secret']) }}
                        @if ($errors->has('dropbox.folder'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('dropbox.folder') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-primary" href="javascript:void(0);"
                        onclick="event.preventDefault(); $('#test-form').attr('action', '{{route('admin.dropbox.update')}}').submit();"
                    >保存する</a>
                    <a class="btn btn-success" href="javascript:void(0);"
                        onclick="event.preventDefault(); $('#test-form').attr('action', '{{route('admin.dropbox.test')}}').submit();"
                    >アクセスをテストする</a>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}

@stop
