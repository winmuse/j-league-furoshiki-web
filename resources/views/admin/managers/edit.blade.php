@extends('layouts.admin')

@section('title', 'クラブチーム編集｜クラブチーム管理')
@inject('AdminClass', 'App\Models\Admin')
@section('content_header_label')
    <h1>クラブチーム編集</h1>
@stop

@php
/** @var App\Models\Admin $manager */
/** @var App\Models\Admin[] $managers */
@endphp

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.managers.update', 'method' => 'PUT', 'id' => 'edit-form']) !!}
    {!! Form::hidden('id', $manager->id) !!}
    {!! Form::hidden('admins[role]', $manager->role) !!}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>連携クラブ</label>
                        {{ Form::select('admins[parent_admin_id]', Arr::pluck($managers, 'name', 'id'), $manager->parent_admin_id,
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
                        {{ Form::text('admins[email]', '',
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
                        {{ Form::text('admins[name]', $manager->name,
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
                        {{ Form::text('admins[name_short]', $manager->name_short,
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
                        {{ Form::text('admins[name_en]', $manager->name_en,
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
                        {{ Form::select('admins[role]', $AdminClass::getRoleOptions(), $manager->role,
                        ['class' => 'form-control' . ($errors->has('admins.role') ? ' is-invalid' : ''), 'id' => 'manager-role']) }}
                        @if ($errors->has('admins.role'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admins.role') }}</strong>
                        </span>
                        @endif
                    </div>
                </div> -->
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
        $('#edit-form').attr('action', "<?php echo route('admin.managers.update'); ?>");
    } else {
        $('#account-name').attr('disabled', 'disabled');
        $('#account-name_short').attr('disabled', 'disabled');
        $('#account-name_en').attr('disabled', 'disabled');
        $('#edit-form').attr('action', "<?php echo route('admin.managers.update.alternative'); ?>");
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
