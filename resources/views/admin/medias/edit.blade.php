@extends('layouts.admin')

@section('title', '素材編集｜素材管理')
@inject('MediaClass', 'App\Models\Media')
@section('content_header_label')
    <h1>素材編集</h1>
@stop

@php
/** @var App\Models\Media $media */
@endphp

@section('content')
    <!-- /.card -->
    {!! Form::open(['route' => 'admin.medias.update', 'method' => 'PUT']) !!}
    {!! Form::hidden('id', $media->id) !!}
    <div class="card">
        <div class="card-body media-edit">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        @if ($media->extension === $MediaClass::EXTENSION_MP4)
                        <video class="video" controls :poster="{{ $media->thumb_url }}">
                            <source src="{{ $media->video_url }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        @else
                        <div class="col-md-12">
                            <img src="{{ $media->source_url }}" alt="" class="image" data-toggle="modal" data-target="#modal-image"/>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>クレジット</label>
                                {{ Form::text('medias[creator]', $media->creator,
                                ['class' => 'form-control', 'id' => 'media-creator', 'disabled' => 'disabled']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>イベント名</label>
                                {{ Form::text('media_metas[event]', $media->meta->event,
                                ['class' => 'form-control', 'id' => 'media-event', 'disabled' => 'disabled']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>試合名</label>
                                {{ Form::text('media_metas[game]', $media->meta->game,
                                ['class' => 'form-control' . ($errors->has('media_metas.game') ? ' is-invalid' : ''), 'id' => 'media-game']) }}
                                @if ($errors->has('media_metas.game'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.game') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>撮影場所</label>
                                {{ Form::text('media_metas[game_place]', $media->meta->game_place,
                                ['class' => 'form-control' . ($errors->has('media_metas.game_place') ? ' is-invalid' : ''), 'id' => 'media-game_place']) }}
                                @if ($errors->has('media_metas.game_place'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.game_place') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>撮影日</label>
                                {{ Form::date('media_metas[game_date]', $media->meta->game_date,
                                ['class' => 'form-control' . ($errors->has('media_metas.game_date') ? ' is-invalid' : ''), 'id' => 'media-game_date']) }}
                                @if ($errors->has('media_metas.game_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.game_date') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>昼／夜</label>
                                {{ Form::select('media_metas[game_time]', ['昼' => '昼', '夜' => '夜'], $media->meta->game_time,
                                ['class' => 'form-control' . ($errors->has('media_metas.game_time') ? ' is-invalid' : ''), 'id' => 'media-game-time', 'placeholder' => 'なし']) }}
                                @if ($errors->has('media_metas.game_time'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.game_time') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>チーム名(ホーム)</label>
                                {{ Form::text('media_metas[home_team]', $media->meta->home_team,
                                ['class' => 'form-control' . ($errors->has('media_metas.home_team') ? ' is-invalid' : ''), 'id' => 'media-home_team']) }}
                                @if ($errors->has('media_metas.home_team'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.home_team') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>チーム名(アウェイ)</label>
                                {{ Form::text('media_metas[away_team]', $media->meta->away_team,
                                ['class' => 'form-control' . ($errors->has('media_metas.away_team') ? ' is-invalid' : ''), 'id' => 'media-away_team']) }}
                                @if ($errors->has('media_metas.away_team'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.away_team') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>選手名</label>
                                {{ Form::text('media_metas[players]', $media->meta->players,
                                ['class' => 'form-control' . ($errors->has('media_metas.players') ? ' is-invalid' : ''), 'id' => 'media-players']) }}
                                @if ($errors->has('media_metas.players'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.players') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>被写体1</label>
                                {{ Form::text('media_metas[subject1]', $media->meta->subject1,
                                ['class' => 'form-control' . ($errors->has('media_metas.subject1') ? ' is-invalid' : ''), 'id' => 'media-subject1']) }}
                                @if ($errors->has('media_metas.subject1'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.subject1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>状態1</label>
                                {{ Form::text('media_metas[state1]', $media->meta->state1,
                                ['class' => 'form-control' . ($errors->has('media_metas.state1') ? ' is-invalid' : ''), 'id' => 'media-state1']) }}
                                @if ($errors->has('media_metas.state1'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.state1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>被写体2</label>
                                {{ Form::text('media_metas[subject2]', $media->meta->subject2,
                                ['class' => 'form-control' . ($errors->has('media_metas.subject2') ? ' is-invalid' : ''), 'id' => 'media-subject2']) }}
                                @if ($errors->has('media_metas.subject2'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.subject2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>状態2</label>
                                {{ Form::text('media_metas[state2]', $media->meta->state2,
                                ['class' => 'form-control' . ($errors->has('media_metas.state2') ? ' is-invalid' : ''), 'id' => 'media-state2']) }}
                                @if ($errors->has('media_metas.state2'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.state2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>被写体3</label>
                                {{ Form::text('media_metas[subject3]', $media->meta->subject3,
                                ['class' => 'form-control' . ($errors->has('media_metas.subject3') ? ' is-invalid' : ''), 'id' => 'media-subject3']) }}
                                @if ($errors->has('media_metas.subject3'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.subject3') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>状態3</label>
                                {{ Form::text('media_metas[state3]', $media->meta->state3,
                                ['class' => 'form-control' . ($errors->has('media_metas.state3') ? ' is-invalid' : ''), 'id' => 'media-state3']) }}
                                @if ($errors->has('media_metas.state3'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.state3') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>グループ</label>
                                {{ Form::text('media_metas[group_name]', $media->meta->group_name,
                                ['class' => 'form-control' . ($errors->has('media_metas.group_name') ? ' is-invalid' : ''), 'id' => 'media-group_name']) }}
                                @if ($errors->has('media_metas.group_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('media_metas.group_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ステータス</label>
                                {{ Form::select('medias[is_done]', $MediaClass::getDones(), $media->is_done,
                                ['class' => 'form-control' . ($errors->has('medias.is_done') ? ' is-invalid' : ''), 'id' => 'media-game-time']) }}
                                @if ($errors->has('medias.is_done'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('medias.is_done') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>検索上位表示</label>
                                {{ Form::select('medias[is_top]', [1 => '表示する', 0 => '表示しない'], $media->is_top,
                                ['class' => 'form-control' . ($errors->has('medias.is_top') ? ' is-invalid' : ''), 'id' => 'media-game-time']) }}
                                @if ($errors->has('medias.is_top'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('medias.is_top') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>その他</label>
                                {{ Form::textarea('media_metas[others]', $media->meta->others,
                                ['class' => 'form-control' . ($errors->has('media_metas.others') ? ' is-invalid' : ''), 'id' => 'media-others']) }}
                                @error('media_metas.others')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="position: fixed; bottom: 28px;">
                <div class="col-md-12">
                    <a href="#" class="btn btn-primary" data-target="#modal-save" data-toggle="modal">保存する</a>
                    <a href="{{ route('admin.medias.index', Session::get('media_search')) }}" class="btn btn-default">キャンセル</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-image">
        <div class="modal-dialog modal-image">
            <div class="modal-content">
                <div class="modal-body" data-dismiss="modal">
                    <img src="{{ $media->source_url }}" alt="" />
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modal-save">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    素材の保存
                </div>
                <div class="modal-body" data-dismiss="modal">
                    この素材を保存しますか？
                </div>
                <div class="modal-footer">
                    {!! Form::submit('保存する', ['class' => 'btn btn-primary']) !!}
                    <button class="btn btn-gray" data-dismiss="modal">キャンセル</button>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {!! Form::close() !!}
@stop
