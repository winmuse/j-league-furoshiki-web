@extends('layouts.admin')

@section('title', 'Balzアカウント一覧｜Balzアカウント管理')
@inject('AdminClass', 'App\Models\Admin')
@section('content_header_label')
    <h1>Balzアカウント一覧<a href="{{ route('admin.balzs.create') }}" class="btn btn-success ml-2">追加</a></h1>
@stop

@php
/** @var App\Models\Admin[] $balzs */
@endphp

@section('content')
    {!! Form::open(['route' => 'admin.balzs.index', 'method' => 'GET']) !!}
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Balzアカウント名</label>
                        {{ Form::text('name', Request::get('name'),
                        ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'id' => 'account-name']) }}
                        @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                {!! Form::hidden('role', $AdminClass::BALZ_ROLE) !!}
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::submit("検索", ["class" => "btn btn-primary"]) !!}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

    <!-- /.card -->
    <div class="card">
        @if (sizeof($balzs) === 0)
        <div class="card-body">
            Balzアカウントはございません。
        </div>
        @else
        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Balzアカウント名</th>
                        <th style="width: 140px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($balzs as $balz)
                    <tr>
                        <td>{{ $balz->id }}</td>
                        <td>{{ $balz->name }}</td>
                        <td class="text-right d-flex justify-content-around" style="width: 140px;">
                            <a href="{{ route('admin.balzs.edit', ['id' => $balz->id]) }}" class="btn btn-sm btn-info">編集</a>
                            <button class="btn btn-sm btn-danger btn-remove" data-id="{{ $balz->id }}">削除</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {!! $balzs->appends(Request::except('_token'))->links() !!}
        </div>
        @endif
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <span style="display: none;" data-toggle="modal" data-target="#modal-delete" id="deleteModal"></span>
    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'admin.balzs.delete', 'method' => 'DELETE']) !!}
                    {!! Form::hidden('id', '', ['id' => 'balz_id']) !!}
                <div class="modal-header">
                    Balzアカウントの削除
                </div>
                <div class="modal-body">
                    このBalzアカウントを削除しますか？
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
        $('#balz_id').attr('value', $(this).data('id'));
        $('#deleteModal').click();
    })
})
</script>
@stop
