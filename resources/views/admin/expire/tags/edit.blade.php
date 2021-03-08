@extends('layouts.admin')

@section('title', '不定期ハッシュタグ編集｜不定期ハッシュタグ管理')
@section('content_header_label')
    <h1>不定期ハッシュタグ編集</h1>
@stop

@php
/** @var App\Models\Master\ExpireTag $tag */
@endphp

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.expire.tags.update', 'method' => 'PUT']) !!}
    {!! Form::hidden('id', $tag->id) !!}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>不定期ハッシュタグ</label>
                        {{ Form::text('tags[name]', $tag->name, 
                        ['class' => 'form-control' . ($errors->has('tags.name') ? ' is-invalid' : ''), 'id' => 'account-name']) }}
                        @if ($errors->has('tags.name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tags.name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>期間開始日</label>
                        {{ Form::date('tags[use_start]', $tag->use_start, 
                        ['class' => 'form-control' . ($errors->has('tags.use_start') ? ' is-invalid' : ''), 'id' => 'use-start']) }}
                        @if ($errors->has('tags.use_start'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tags.use_start') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>期間限定日</label>
                        {{ Form::date('tags[expire_at]', $tag->expire_at, 
                        ['class' => 'form-control' . ($errors->has('tags.expire_at') ? ' is-invalid' : ''), 'id' => 'expire-at']) }}
                        @if ($errors->has('tags.expire_at'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tags.expire_at') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::submit('保存する', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ route('admin.expire.tags.index', Session::get('expire_tag_search')) }}" class="btn btn-default">キャンセル</a>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
