@extends('layouts.admin')

@section('title', 'クラブチーム新規追加｜クラブチーム管理')
@inject('AdminClass', 'App\Models\Admin')

@section('content_header_label')
    <h1>クラブチーム新規追加</h1>
@stop

@php
/** @var App\Models\Admin[] $managers */
@endphp


@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.managers.save', 'method' => 'POST', 'id' => 'create-form']) !!}
    {!! Form::hidden('admins[role]', $AdminClass::CLUB_ROLE) !!}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>連携クラブ</label>
                        {{ Form::select('admins[parent_admin_id]', Arr::pluck($managers, 'name', 'id'), old('admins.parent_admin_id'),
                        ['class' => 'form-control' . ($errors->has('admins.parent_admin_id') ? ' is-invalid' : ''), 'id' => 'parent-clubs', 'placeholder' => 'なし']) }}
                        @if ($errors->has('admins.parent_admin_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.parent_admin_id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
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
                        <label>クラブチーム名</label>
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
                        <label>略名</label>
                        {{ Form::text('admins[name_short]', old('admins.name_short'),
                        ['class' => 'form-control' . ($errors->has('admins.name_short') ? ' is-invalid' : ''), 'id' => 'account-name_short']) }}
                        @if ($errors->has('admins.name_short'))
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.name_short') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>英名</label>
                        {{ Form::text('admins[name_en]', old('admins.name_en'),
                        ['class' => 'form-control' . ($errors->has('admins.name_en') ? ' is-invalid' : ''), 'id' => 'account-name_en']) }}
                        @if ($errors->has('admins.name_en'))
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.name_en') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

            <!-- <div class="col-md-3">
                    <div class="form-group">
                        <label>区分</label>
                        {{ Form::select('admins[role]', $AdminClass::getRoleOptions(), old('admins.role'),
                        ['class' => 'form-control' . ($errors->has('admins.role') ? ' is-invalid' : ''), 'id' => 'account-status']) }}
                        @if ($errors->has('admins.role'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.role') }}</strong>
                        </span>
                        @endif
                    </div>
                </div> -->
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
                    <a href="{{ route('admin.managers.index', Session::get('manager_search')) }}" class="btn btn-default">キャンセル</a>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('js')
<script>
function changeVal(obj) {
    if (obj.val() === '') {
        $('#account-name').removeAttr('disabled');
        $('#account-name_short').removeAttr('disabled');
        $('#account-name_en').removeAttr('disabled');
        $('#create-form').attr('action', "<?php echo route('admin.managers.save'); ?>");
    } else {
        $('#account-name').attr('disabled', 'disabled');
        $('#account-name_short').attr('disabled', 'disabled');
        $('#account-name_en').attr('disabled', 'disabled');
        $('#create-form').attr('action', "<?php echo route('admin.managers.save.alternative'); ?>");
    }
}
$(document).ready(function() {
    $('#parent-clubs').change(function() {
        changeVal($(this));
    })

    changeVal($('#parent-clubs'));
})
</script>
@stop