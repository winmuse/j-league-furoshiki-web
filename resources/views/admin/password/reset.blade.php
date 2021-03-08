@extends('layouts.admin')

@section('title', 'パスワードのリセット')
@section('content_header_label')
    <h1>パスワードのリセット</h1>
@stop

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.password.reset', 'method' => 'POST', 'id' => 'edit-form']) !!}
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>以前のパスワード</label>
                        <input type="password"
                               name="password_old"
                               class="form-control @error('password_old') is-invalid @enderror"
                               id="password_old"/>
                        @error('password_old')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>新しいパスワード</label>
                        <input type="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"/>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>パスワード確認</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               id="password_confirmation"/>
                        @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">保存する</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('js')
<script>
</script>
@stop
