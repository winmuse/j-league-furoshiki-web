@extends('layouts.admin')

@section('title', 'LINEアカウント一覧｜LINEアカウント管理')
@section('content_header_label')
    <h1>LINEアカウント一覧</h1>
@stop

@php
/** @var App\Models\Master\Tag[] $lines */
@endphp

@section('content')
    <!-- /.card -->
    <div class="card">
        @if (sizeof($lines) === 0)
        <div class="card-body">
            LINEアカウントはございません。
        </div>
        @else
        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>選手</th>
                        <th>URL</th>
                      <th>有効フラグ</th>
                        <th style="width: 140px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lines as $line)
                    <tr>
                        <td>{{ $line->id }}</td>
                        <td>{{ $line->user->name }}</td>
                        <td>{{ $line->auth_url }}</td>
                        <td>{{ $line->valid_flag === 0 ? '無効' : '有効' }}</td>
                        <td class="text-right d-flex justify-content-around" style="width: 140px;">
                            <a href="{{ route('admin.lines.edit', ['id' => $line->id]) }}" class="btn btn-sm
                            btn-info">編集</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {!! $lines->appends(Request::except('_token'))->links() !!}
        </div>
        @endif
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@stop
