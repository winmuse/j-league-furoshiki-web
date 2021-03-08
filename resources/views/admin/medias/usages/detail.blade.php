@extends('layouts.admin')

@section('title', '画像・動画の利用履歴詳細')
@inject('MediaClass', 'App\Models\Media')
@inject('AdminClass', 'App\Models\Admin')
@section('content_header_label')
    <h1>画像・動画の利用履歴詳細</h1>
@stop

@php
/** @var App\Models\Media $media */
@endphp

@section('content')
    <!-- /.card -->
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
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>イベント名</label>
                                {{ Form::text('media_metas[event]', $media->meta->event,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>試合名</label>
                                {{ Form::text('media_metas[game]', $media->meta->game,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>撮影場所</label>
                                {{ Form::text('media_metas[game_place]', $media->meta->game_place,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>撮影日</label>
                                {{ Form::date('media_metas[game_date]', $media->meta->game_date,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>昼／夜</label>
                                {{ Form::select('media_metas[game_time]', ['昼' => '昼', '夜' => '夜'], $media->meta->game_time,
                                ['disabled' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>チーム名(ホーム)</label>
                                {{ Form::text('media_metas[home_team]', $media->meta->home_team,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>チーム名(アウェイ)</label>
                                {{ Form::text('media_metas[away_team]', $media->meta->away_team,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>選手名</label>
                                {{ Form::text('media_metas[players]', $media->meta->players,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>被写体1</label>
                                {{ Form::text('media_metas[subject1]', $media->meta->subject1,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>状態1</label>
                                {{ Form::text('media_metas[state1]', $media->meta->state1,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>被写体2</label>
                                {{ Form::text('media_metas[subject2]', $media->meta->subject2,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>状態2</label>
                                {{ Form::text('media_metas[state2]', $media->meta->state2,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>被写体3</label>
                                {{ Form::text('media_metas[subject3]', $media->meta->subject3,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>状態3</label>
                                {{ Form::text('media_metas[state3]', $media->meta->state3,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>グループ</label>
                                {{ Form::text('media_metas[group_name]', $media->meta->group_name,
                                ['readonly' => true, 'class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <label>利用履歴</label>
                        </div>
                        @if (auth()->user()->role === $AdminClass::CLUB_ROLE)
                            @php
                                $articles = $media->clubArticles;
                            @endphp
                        @else
                            @php
                                $articles = $media->articles;
                            @endphp
                        @endif
                        @foreach ($articles as $article)
                            <div class="col-md-12">
                                <div>
                                    <label>記事名：{{ $article->description }}</label>
                                </div>
                                <div>
                                    <label>投稿者名：{{ $article->user->name }}</label>
                                </div>
                                <div>
                                    <div class="col-md-12">
                                    @foreach ($article->sns_urls as $key => $sns)
                                        <a href="{{ $sns->url }}" target="_blank">{{ $sns->sns }}</a>
                                        @if ($key != sizeof($article->sns_urls) - 1)
                                        <span>、</span>
                                        @endif
                                    @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('admin.media.usage.index') }}" class="btn btn-default">キャンセル</a>
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
@stop
