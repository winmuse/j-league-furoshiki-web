@extends('layouts.admin')

@section('title', 'LINEアカウントの編集｜LINEアカウント管理')
@inject('LineClass', 'App\Models\LineCredential')
@section('content_header_label')
    <h1>LINEアカウントの編集</h1>
@stop

@php
/** @var App\Models\LineCredential $line */
@endphp

@section('content')
    <!-- /.card -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">連携手順</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <p>連携手順が入ります。</p>
        </div>
    </div>
    {!! Form::open(['route' => 'admin.lines.update', 'method' => 'PUT']) !!}
    {!! Form::hidden('id', $line->id) !!}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>選手名</label>
                        <input type="text" disabled="true" value="{{ $line->user->name }}" class="form-control" />
                    </div>
                </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label>名前</label>
                  {{ Form::text('lines[name]', $line->access_token,
                  ['class' => 'form-control' . ($errors->has('lines.name') ? ' is-invalid' : '')]) }}
                  @if ($errors->has('lines.name'))
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('lines.name') }}</strong>
                        </span>
                  @endif
                </div>
              </div>
              <div class="col-md-12">
                    <div class="form-group">
                        <label>Access Token</label>
                        {{ Form::text('lines[access_token]', $line->access_token,
                        ['class' => 'form-control' . ($errors->has('lines.access_token') ? ' is-invalid' : '')]) }}
                        @if ($errors->has('lines.access_token'))
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('lines.access_token') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Channel Secret</label>
                        {{ Form::text('lines[channel_secret]', $line->channel_secret,
                        ['class' => 'form-control' . ($errors->has('lines.channel_secret') ? ' is-invalid' : '')]) }}
                        @if ($errors->has('lines.channel_secret'))
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('lines.channel_secret') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>ステータス</label>
                        {{ Form::select('lines[valid_flag]', $LineClass::getFlagOptions(), $line->valid_flag,
                        ['class' => 'form-control' . ($errors->has('lines.valid_flag') ? ' is-invalid' : '')]) }}
                        @if ($errors->has('lines.valid_flag'))
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('lines.valid_flag') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::submit('保存する', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ route('admin.lines.index') }}" class="btn btn-default">キャンセル</a>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
