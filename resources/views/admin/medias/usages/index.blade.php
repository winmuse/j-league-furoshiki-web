@extends('layouts.admin')

@section('title', '画像・動画の利用履歴')
@inject('AdminClass', 'App\Models\Admin')
@php
/** @var App\Models\Media[] $medias */
/** @var App\Models\User[] $users */
@endphp

@section('content')
    {!! Form::open(['route' => 'admin.media.usage.index', 'method' => 'GET']) !!}
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
                    <div class="form-group" id="club_search">
                        <label>クラブ名</label>

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group" id="player_search">
                        <label>投稿者名</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>投稿日（から）</label>
                        {{ Form::date('created_start', Request::get('created_start'),
                        ['class' => 'form-control search-form-control' . ($errors->has('created_start') ? ' is-invalid' : ''), 'id' => 'tag-created_start']) }}
                        @if ($errors->has('created_start'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('created_start') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>投稿日（まで）</label>
                        {{ Form::date('created_end', Request::get('created_end'),
                        ['class' => 'form-control search-form-control' . ($errors->has('created_end') ? ' is-invalid' : ''), 'id' => 'tag-created_end']) }}
                        @if ($errors->has('created_end'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('created_end') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>上位表示メタデータ</label>
                        {{ Form::select('is_top', [1 => '含む', 0 => '含まない'], Request::get('is_top'),
                        ['class' => 'form-control search-form-control' . ($errors->has('is_top') ? ' is-invalid' : ''), 'id' => 'media-is_top', 'placeholder' => 'すべて']) }}
                        @if ($errors->has('is_top'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('is_top') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>投稿内容</label>
                        {{ Form::text('content', Request::get('content'),
                        ['class' => 'form-control search-form-control' . ($errors->has('content') ? ' is-invalid' : ''), 'id' => 'tag-content']) }}
                        @if ($errors->has('content'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('content') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>ハッシュタグ</label>
                        {{ Form::text('tag', Request::get('tag'),
                        ['class' => 'form-control search-form-control' . ($errors->has('tag') ? ' is-invalid' : ''), 'id' => 'tag-tag']) }}
                        @if ($errors->has('tag'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tag') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    {!! Form::submit("検索する", ["class" => "btn btn-primary"]) !!}
                    <button type="button" class="btn btn-success" id="btnReset">リセット</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    
    <!-- /.card -->
    <div class="card">
        @if (sizeof($medias) === 0)
        <div class="card-body">
            利用履歴はありません。
        </div>
        @else
        <div class="card-body">
            <h5>{{ $medias->total() }}件の利用履歴</h5><br/>
            <div class="row" style="margin-bottom: 12px">
                <div class="col-md-12 d-flex" style="justify-content: flex-end;">
                    {!! Form::open(['route' => 'admin.media.usage.export', 'method' => 'POST']) !!}
                    {!! Form::hidden('club', Request::get('club')) !!}
                    {!! Form::hidden('players', Request::get('players')) !!}
                    {!! Form::hidden('is_top', Request::get('is_top')) !!}
                    {!! Form::hidden('created_start', Request::get('created_start')) !!}
                    {!! Form::hidden('created_end', Request::get('created_end')) !!}
                    {!! Form::hidden('content', Request::get('content')) !!}
                    {!! Form::hidden('tag', Request::get('tag')) !!}
                    {{ Form::submit('CSVダウンロード', ['class' => 'btn btn-success']) }}
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="example2" class="table table-bordered table-hover medias">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="max-width: 250px;">イベント名</th>
                                <th>利用した記事件数</th>
                                <th>登録日</th>
                                <th style="max-width: 200px;">試合名（動画はファイル名）</th>
                                <th>選手名</th>
                                <!-- <th style="width: 200px;">利用日時</th> -->
                                <th style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($medias as $media)
                            <tr>
                                <td class="thumbnail">
                                    <a href="{{ route('admin.media.usage.detail', ['id' => $media->id]) }}">
                                        <img src="{{$media->thumb_url}}" alt="{{$media->filename}}" />
                                        @if ($media->extension === 'mp4')
                                            <img src="/images/video.png" class="video_play" alt="video_play" />
                                        @endif
                                    </a>
                                </td>
                                <td style="max-width: 250px;">
                                    <span style="display: inline-block;height: 20px;line-height: 20px; border: 0px;overflow: hidden;">{{ $media->meta->event }}</span>
                                </td>
                                @if (auth()->user()->role === $AdminClass::CLUB_ROLE)
                                    <td>{{ sizeof($media->clubArticles) }}</td>
                                @else
                                    <td>{{ sizeof($media->articles) }}</td>
                                @endif
                                <td>
                                    {{ $media->created_at->format("Y-m-d") }}
                                </td>
                                <td style="max-width: 200px;">
                                    <span style="display: inline-block;height: 20px;line-height: 20px; border: 0px;overflow: hidden;">{{ $media->extension === 'jpg' ? $media->meta->game : $media->filename }}</span>
                                </td>
                                <td>
                                    {{ $media->meta->players }}
                                </td>
                                <td class="text-right d-flex justify-content-around td-btns">
                                    <a href="{{ route('admin.media.usage.detail', ['id' => $media->id]) }}" class="btn btn-sm btn-info">詳細</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {!! $medias->appends(Request::except('_token'))->links() !!}
        </div>
        @endif
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@stop

@section('js')
<script>
var clubs = [
    <?php 
    foreach ($clubs as $club) {
        echo "{id: " . $club->id . ", name: '" . $club->name . "'},";
    }
    ?>
];
var players = [
    <?php 
    foreach ($users as $user) {
        echo "{id: " . $user->id . ", name: '" . $user->name . "', club: " . $user->admin_id . "},";
    }
    ?>
];
var selectedClub = parseInt(<?php echo Request::get('club') ? Request::get('club') : -1 ?>);
var selectedPlayer = parseInt(<?php echo Request::get('players') ? Request::get('players') : -1 ?>);

function initClubs() {
    var clubSearch = $('#club_search').html("");
    clubSearch.append($("<label>").text('クラブ名'));
    
    var clubSearchSelect = $("<select>").addClass('form-control').addClass('search-form-control').attr('name', 'club').attr('id', 'tag-club');
    var option = $("<option>").attr('value', -1).text("すべて");
    if (selectedClub === -1) {
        option.attr("selected", "selected");
    }
    clubSearchSelect.append(option);

    if (clubs !== undefined && clubs.length > 0) {
        clubs.map(club => {
            var option = $("<option>").attr('value', club.id).text(club.name);
            if (selectedClub === parseInt(club.id)) {
                option.attr("selected", "selected");
            }
            clubSearchSelect.append(option);
        })
    }

    clubSearch.append(clubSearchSelect);
}

function refreshPlayers() {
    var playerSearch = $('#player_search').html("");
    playerSearch.append($("<label>").text('投稿者名'));
    var showPlayers = players.filter(player => {
        if (selectedClub === -1) return true;

        if (selectedClub === parseInt(player.club)) return true;
        return false;
    })

    var playerSearchSelect = $("<select>").addClass('form-control').addClass('search-form-control').attr('name', 'players').attr('id', 'tag-player');
    var option = $("<option>").attr('value', -1).text("すべて");
    if (selectedPlayer === -1) {
        option.attr("selected", "selected");
    }
    playerSearchSelect.append(option);

    if (showPlayers !== undefined && showPlayers.length > 0) {
        showPlayers.map(player => {
            var option = $("<option>").attr('value', player.id).text(player.name);
            if (selectedPlayer === parseInt(player.id)) {
                option.attr("selected", "selected");
            }
            playerSearchSelect.append(option);
        })
    }

    playerSearch.append(playerSearchSelect);    
}
$(document).ready(function() {
    $('#btnReset').click(function() {
        $('.search-form-control').map(function() {
            if ($(this).attr('id') === 'tag-club' || $(this).attr('id') === 'tag-player') {
                $(this).val(-1);
                selectedClub = -1;
                selectedPlayer = -1;
                refreshPlayers();
            } else {
                $(this).val('');
            }
        })
    })

    initClubs();
    refreshPlayers();

    $('#tag-club').change(function() {
        selectedClub = parseInt($(this).val());
        selectedPlayer = -1;
        refreshPlayers();
    })
})
</script>
@stop