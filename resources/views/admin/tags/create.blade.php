@extends('layouts.admin')

@section('title', '定型ハッシュタグ新規追加｜定型ハッシュタグ管理')

@section('content_header_label')
    <h1>定型ハッシュタグ新規追加</h1>
@stop

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.tags.save', 'method' => 'POST']) !!}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>定型ハッシュタグ</label>
                        {{ Form::text('tags[name]', old('tags.name'), 
                        ['class' => 'form-control' . ($errors->has('tags.name') ? ' is-invalid' : ''), 'id' => 'account-email']) }}
                        @if ($errors->has('tags.name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tags.name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::submit('保存する', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ route('admin.tags.index', Session::get('tag_search')) }}" class="btn btn-default">キャンセル</a>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
