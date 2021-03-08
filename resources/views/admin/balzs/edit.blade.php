@extends('layouts.admin')

@section('title', 'Balzアカウント編集｜Balzアカウント管理')
@inject('AdminClass', 'App\Models\Admin')
@section('content_header_label')
    <h1>Balzアカウント編集</h1>
@stop

@php
/** @var App\Models\Admin $balz */
@endphp

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.balzs.update', 'method' => 'PUT']) !!}
    {!! Form::hidden('id', $balz->id) !!}
    {!! Form::hidden('admins[role]', $balz->role) !!}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>メールアドレス</label>
                        <input type="text" disabled="true" value="{{ $balz->email }}" class="form-control" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Balzアカウント名</label>
                        {{ Form::text('admins[name]', $balz->name,
                        ['class' => 'form-control' . ($errors->has('admins.name') ? ' is-invalid' : ''), 'id' => 'manager-name']) }}
                        @error('admins.name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.name') }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>パスワード</label>
                        <input type="text" name="admins[password]"
                               class="form-control @error('admins.password') is-invalid @enderror"/>
                        @error('admins.password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::submit('保存する', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ route('admin.balzs.index', Session::get('balz_search')) }}" class="btn btn-default">キャンセル</a>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
