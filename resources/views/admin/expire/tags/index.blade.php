@extends('layouts.admin')

@section('title', '不定型ハッシュタグ一覧｜不定型ハッシュタグ管理')
@section('content_header_label')
    <h1>不定型ハッシュタグ一覧<a href="{{ route('admin.expire.tags.create') }}" class="btn btn-success ml-2">追加</a></h1>
@stop

@php
/** @var App\Models\Master\ExpireTag[] $tags */
@endphp

@section('content')
    {!! Form::open(['route' => 'admin.expire.tags.index', 'method' => 'GET']) !!}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">検索条件</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>不定型ハッシュタグ</label>
                        {{ Form::text('name', Request::get('name'), 
                        ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'id' => 'tag-name']) }}
                        @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4" style="margin-top: 30px;">
                    {!! Form::submit("検索", ["class" => "btn btn-primary"]) !!}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    
    <!-- /.card -->
    <div class="card">
        @if (sizeof($tags) === 0)
        <div class="card-body">
            不定型ハッシュタグはございません。
        </div>
        @else
        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>不定型ハッシュタグ</th>
                        <th>作成者</th>
                        <th>期間開始日</th>
                        <th>期間限定日</th>
                        <th style="width: 140px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tags as $tag)
                    <tr>
                        <td>{{ $tag->id }}</td>
                        <td>{{ $tag->name }}</td>
                        <td>{{ $tag->club ? $tag->club->name : 'リーグ管理者' }}</td>
                        <td>{{ $tag->use_start ? $tag->use_start->format('Y年m月d日') : '' }}</td>
                        <td>{{ $tag->expire_at ? $tag->expire_at->format('Y年m月d日') : '' }}</td>
                        <td class="text-right d-flex justify-content-around" style="width: 140px;">
                            <a href="{{ route('admin.expire.tags.edit', ['id' => $tag->id]) }}" class="btn btn-sm btn-info">編集</a>
                            <button class="btn btn-sm btn-danger btn-remove" data-id="{{ $tag->id }}">削除</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            {!! $tags->appends(Request::except('_token'))->links() !!}
        </div>
        @endif
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    
    <span style="display: none;" data-toggle="modal" data-target="#modal-delete" id="deleteModal"></span>
    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'admin.expire.tags.delete', 'method' => 'DELETE']) !!}
                    {!! Form::hidden('id', '', ['id' => 'tag_id']) !!}
                <div class="modal-header">
                    不定型ハッシュタグの削除
                </div>
                <div class="modal-body">
                    この不定型ハッシュタグを削除しますか？
                </div>
                <div class="modal-footer">
                    {!! Form::submit('削除する', ['class' => 'btn btn-danger']) !!}
                    <button class="btn btn-gray" data-dismiss="modal">キャンセル</button>
                </div>
                {!! Form::close() !!}
            </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('.btn-remove').click(function() {
        $('#tag_id').attr('value', $(this).data('id'));
        $('#deleteModal').click();
    })
})
</script>
@stop
