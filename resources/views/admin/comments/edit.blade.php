@extends('layouts.admin')

@section('title', '定型テキスト編集｜定型テキスト管理')
@section('content_header_label')
    <h1>定型テキスト編集</h1>
@stop

@php
/** @var App\Models\Master\Comment $comment */
@endphp

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.comments.update', 'method' => 'PUT']) !!}
    {!! Form::hidden('id', $comment->id) !!}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>定型テキスト</label>
                        {{ Form::text('comments[name]', $comment->name,
                        ['class' => 'form-control' . ($errors->has('comments.name') ? ' is-invalid' : ''), 'id' => 'account-name']) }}
                        @if ($errors->has('comments.name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('comments.name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::submit('保存する', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ route('admin.comments.index', Session::get('comment_search')) }}" class="btn btn-default">キャンセル</a>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
