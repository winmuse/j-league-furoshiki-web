@extends('layouts.admin')

@section('title', '投稿者一覧｜投稿者管理')
@inject('UserClass', 'App\Models\User')
@inject('AdminClass', 'App\Models\Admin')
@section('content_header_label')
    <h1>投稿者一覧
        <a href="{{ route('admin.accounts.create') }}" class="btn btn-success ml-2 d-none">追加</a>
    </h1>
@stop

@php
/** @var App\Models\User[] $accounts */
/** @var App\Models\Admin[] $clubs */
@endphp

@section('content')
    {!! Form::open(['route' => 'admin.accounts.index', 'method' => 'GET']) !!}
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
                        <label>選手番号</label>
                        {{ Form::text('player_no', Request::get('player_no'),
                        ['class' => 'form-control' . ($errors->has('player_no') ? ' is-invalid' : ''), 'id' => 'account-player_no']) }}
                        @if ($errors->has('player_no'))
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('player_no') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                @if (Auth::user()->role === $AdminClass::JLEAGUE_ROLE)
                <div class="col-md-3">
                    <div class="form-group">
                        <label>クラブ</label>
                        <select class="form-control" name="club">
                            <option value="-1" {{ Request::get('club') && intval(Request::get('club')) === -1 ? 'selected' : ''}} >すべて</option>
                            @foreach ($clubs as $club)
                            <option value="{{ $club->id }}" {{ Request::get('club') && $club->id === intval(Request::get('club')) ? 'selected' : ''}} >{{$club->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <label>投稿者名</label>
                        {{ Form::text('name', Request::get('name'),
                        ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'id' => 'account-name']) }}
                        @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>ステータス</label>
                        {{ Form::select('status', $UserClass::getStatusOptions(), Request::get('status'),
                        ['class' => 'form-control' . ($errors->has('status') ? ' is-invalid' : ''), 'id' => 'account-status', 'placeholder' => 'すべて']) }}
                        @if ($errors->has('status'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('status') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
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
        <div class="card-body">
            <div class="row" style="margin-bottom: 12px">
                <div class="col-md-12 d-flex" style="justify-content: flex-end;">
                    @if (Auth::user()->role === $AdminClass::JLEAGUE_ROLE)
                        <div style="margin-right: 20px;">
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#modal-import" class="btn btn-warning">
                                選手データをアップロード
                            </a>
                        </div>
                        <div style="margin-right: 20px;">
                            <a href="{{ route('admin.accounts.export.template') }}" class="btn btn-secondary">
                                テンプレートダウンロード
                            </a>
                        </div>
                        <div style="margin-right: 20px;">
                            <a href="{{ route('admin.accounts.export.all') }}" class="btn btn-success">
                                最新選手データダウンロード
                            </a>
                        </div>
                    @endif
                    <div style="line-height: 40px;">
                        件数：{{ ($accounts->currentPage() - 1) * $accounts->perPage() + 1 }} ~ {{ (($accounts->currentPage()) * $accounts->perPage() < $accounts->total() ? ($accounts->currentPage()) * $accounts->perPage() : $accounts->total()) }} / {{ $accounts->total() }}件
                    </div>
                </div>
            </div>
            @if (sizeof($accounts) === 0)
            <div class="row">
                <div class="col-md-12">
                    投稿者アカウントはございません。
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-md-12">
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>選手番号</th>
                                @if (Auth::user()->role === $AdminClass::JLEAGUE_ROLE)
                                <th>クラブ名</th>
                                @endif
                                <th>選手氏名漢字</th>
                                <th>選手氏名英字</th>
                                @if (Auth::user()->role !== $AdminClass::JLEAGUE_ROLE)
{{--                                <th>メールアドレス</th>--}}
{{--                                <th>電話番号</th>--}}
                                @endif
                                <th>ステータス</th>
                                <th style="width: 140px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                            <tr>
                                <td>{{ $account->player_no }}</td>
                                @if (Auth::user()->role === $AdminClass::JLEAGUE_ROLE)
                                <td>{{ empty($account->profile->club) ? '' : $account->profile->club->name }}</td>
                                @endif
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->name_en }}</td>
                                @if (Auth::user()->role !== $AdminClass::JLEAGUE_ROLE)
{{--                                <td>{{ $account->email }}</td>--}}
{{--                                <td>{{ $account->profile->mobile }}</td>--}}
                                @endif
                                <td><span class="badge bg-{{$account->status === $UserClass::STATUS_ACTIVE ? 'success' : 'danger'}}">{{ $account->status_label }}</span></td>
                                <td class="text-right d-flex justify-content-around" style="width: 140px;">
                                    <a href="{{ route('admin.accounts.edit', ['id' => $account->id]) }}" class="btn btn-sm btn-info">編集</a>
                                    <button class="btn btn-sm btn-danger btn-remove" data-id="{{ $account->id }}">削除</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {!! $accounts->appends(Request::except('_token'))->links() !!}
                </div>
            </div>
            @endif
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="modal fade" id="modal-import">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'admin.accounts.import', 'method' => 'POST', 'id' => 'csvForm', 'enctype' => 'multipart/form-data']) !!}
                <div class="modal-header">
                    登録選手一覧データをCSVで取得する
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 small">
                            .xlsxや.csvファイルを選択してください。
                        </div>
                    </div>
                    <div class="row cursor" id="btnCSV">
                        <div class="col-md-4 file-label">
                            ファイル名
                        </div>
                        <div class="col-md-8 selected-file" id="selectedFile">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 red small hidden" id="fileAlert">
                            正しいファイルを選択してください。
                        </div>
                    </div>
                    {{ Form::file('csv_file', ['id' => 'csv_file', 'class' => 'form-control', 'style' => 'display: none;', 'accept' => '.xlsx']) }}
                </div>
                <div class="modal-footer">
                    {!! Form::submit('アップロード', ['class' => 'btn btn-primary', 'disabled' => 'disabled', 'id' => 'btnCSVImport']) !!}
                    <button class="btn btn-gray" data-dismiss="modal">キャンセル</button>
                </div>
                {!! Form::close() !!}
            </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <span style="display: none;" data-toggle="modal" data-target="#modal-delete" id="deleteModal"></span>
    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'admin.accounts.delete', 'method' => 'DELETE']) !!}
                    {!! Form::hidden('id', '', ['id' => 'account_id']) !!}
                <div class="modal-header">
                    投稿者の削除
                </div>
                <div class="modal-body">
                    この投稿者を削除しますか？
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

@section('adminlte_css')
<style>
.small {
    font-size: 14px;
}
.red {
    color: #ff0000;
}

.file-label {
    height: 40px;
    line-height: 40px;
    background: #eee;
    text-align: center;
    margin: 20px 0px;
}

.selected-file {
    height: 40px;
    line-height: 40px;
    overflow: hidden;
    border: 1px solid #eee;
    margin: 20px 0px;
}
.hidden {
    display: none;
}
.cursor {
    cursor: pointer;
}

</style>
@stop
@section('js')
<script>
$(document).ready(function() {
    $('#btnCSV').click(function() {
        $('#csv_file').click();
    })

    $('#csv_file').change(function(e) {
        var filename = $(this).val().replace(/C:\\fakepath\\/i, '');

        $('#selectedFile').text(filename);

        if (filename.includes('.xlsx') || filename.includes('.csv')) {
            $('#fileAlert').addClass('hidden');
            $('#btnCSVImport').removeAttr('disabled');
            return;
        }
        $('#fileAlert').removeClass('hidden');
        $('#btnCSVImport').attr('disabled', 'disabled');
    })

    $('.btn-remove').click(function() {
        $('#account_id').attr('value', $(this).data('id'));
        $('#deleteModal').click();
    })

    $('#modal-import').on('hidden.bs.modal', function() {
        $('#selectedFile').text('');
        $('#fileAlert').addClass('hidden');
        $('#btnCSVImport').attr('disabled', 'disabled');
        $('#csv_file').val('');
    })
})
</script>
@stop
