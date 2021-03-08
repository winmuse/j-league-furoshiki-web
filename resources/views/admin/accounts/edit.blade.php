@extends('layouts.admin')

@section('title', '投稿者編集｜投稿者管理')
@inject('UserClass', 'App\Models\User')
@inject('AdminClass', 'App\Models\Admin')
@section('content_header_label')
    <h1>投稿者編集</h1>
@stop

@php
/** @var App\Models\User $account */
/** @var App\Models\Admin[] $clubs */
@endphp

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.accounts.update', 'method' => 'PUT']) !!}
    {!! Form::hidden('id', $account->id) !!}
    <div class="card">
        <div class="card-body">
            <div class="row">
            @if (Auth::user()->role === $AdminClass::JLEAGUE_ROLE)
                <div class="col-md-3">
                    <div class="form-group">
                        <label>クラブ</label>
                        <select class="form-control {{$errors->has('account_profiles.admin_id') ? ' is-invalid' : ''}}" name="account_profiles[admin_id]">
                            @foreach ($clubs as $club)
                            <option value="{{ $club->id }}" {{ $club->id === $account->profile->admin_id ? 'selected' : ''}} >{{$club->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('account_profiles.admin_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('account_profiles.admin_id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            @else
                {!! Form::hidden('account_profiles[admin_id]', $account->profile->admin_id) !!}
            @endif
                {!! Form::hidden('users[email]', $account->email) !!}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>メールアドレス</label>
                        {{ Form::text('users[email]', '', 
                        ['class' => 'form-control' . ($errors->has('users.email') ? ' is-invalid' : ''), 'id' => 'users-email']) }}
                        @if ($errors->has('users.email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('users.email') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>投稿者名</label>
                        {{ Form::text('users[name]', $account->name, 
                        ['class' => 'form-control' . ($errors->has('users.name') ? ' is-invalid' : ''), 'id' => 'account-name']) }}
                        @if ($errors->has('users.name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('users.name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                @if (Auth::user()->role !== $AdminClass::JLEAGUE_ROLE)
                <div class="col-md-3">
                    <div class="form-group">
                        <label>電話番号</label>
                        {{ Form::text('account_profiles[mobile]', '', 
                        ['class' => 'form-control' . ($errors->has('account_profiles.mobile') ? ' is-invalid' : ''), 'id' => 'account-profile-mobile']) }}
                        @if ($errors->has('account_profiles.mobile'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('account_profiles.mobile') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                @else
                {!! Form::hidden('account_profiles[mobile]', $account->profile->mobile) !!}
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <label>ステータス</label>
                        {{ Form::select('users[status]', $UserClass::getStatusOptions(), $account->status, 
                        ['class' => 'form-control' . ($errors->has('users.status') ? ' is-invalid' : ''), 'id' => 'account-status']) }}
                        @if ($errors->has('users.status'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('users.status') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>パスワード</label>
                        {{ Form::password('users[password]', 
                        ['class' => 'form-control' . ($errors->has('users.password') ? ' is-invalid' : ''), 'id' => 'account-password']) }}
                        @if ($errors->has('users.password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('users.password') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>パスワードの確認</label>
                        {{ Form::password('users[password_confirmation]', 
                        ['class' => 'form-control' . ($errors->has('users.password_confirmation') ? ' is-invalid' : ''), 'id' => 'account-password_confirmation']) }}
                        @if ($errors->has('users.password_confirmation'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('users.password_confirmation') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::submit('保存する', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ route('admin.accounts.index', Session::get('account_search')) }}" class="btn btn-default">キャンセル</a>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
