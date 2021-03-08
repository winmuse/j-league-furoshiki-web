@extends('layouts.admin')

@section('title', 'Balzアカウント新規追加｜Balzアカウント管理')
@inject('AdminClass', 'App\Models\Admin')

@section('content_header_label')
    <h1>Balzアカウント新規追加</h1>
@stop

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.balzs.save', 'method' => 'POST']) !!}
    {!! Form::hidden('admins[role]', $AdminClass::BALZ_ROLE) !!}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>メールアドレス</label>
                        {{ Form::email('admins[email]', old('admins.email'),
                        ['class' => 'form-control' . ($errors->has('admins.email') ? ' is-invalid' : ''), 'id' => 'account-email']) }}
                        @if ($errors->has('admins.email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.email') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Balzアカウント名</label>
                        {{ Form::text('admins[name]', old('admins.name'),
                        ['class' => 'form-control' . ($errors->has('admins.name') ? ' is-invalid' : ''), 'id' => 'account-name']) }}
                        @if ($errors->has('admins.name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>パスワード</label>
                        {{ Form::password('admins[password]',
                        ['class' => 'form-control' . ($errors->has('admins.password') ? ' is-invalid' : ''), 'id' => 'account-password']) }}
                        @if ($errors->has('admins.password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.password') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>パスワードの確認</label>
                        {{ Form::password('admins[password_confirmation]',
                        ['class' => 'form-control' . ($errors->has('admins.password_confirmation') ? ' is-invalid' : ''), 'id' => 'account-password_confirmation']) }}
                        @if ($errors->has('admins.password_confirmation'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.password_confirmation') }}</strong>
                        </span>
                        @endif
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
