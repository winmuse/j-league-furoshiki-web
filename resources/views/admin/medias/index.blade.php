@extends('layouts.admin')

@section('title', '素材一覧｜素材管理')
@inject('MediaClass', 'App\Models\Media')
@inject('Carbon', 'Carbon\Carbon')
@php
use App\Models\Media;
/** @var Media[] $medias */
@endphp

@section('content')
    {!! Form::open(['id' => 'search-form', 'route' => 'admin.medias.index', 'method' => 'GET']) !!}
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
                        <label>コンテンツ分類</label>
                        {{ Form::select('extension', ['mp4' => '動画', 'jpg' => '静止画'], Request::get('extension'),
                        ['class' => 'form-control search-form-control' . ($errors->has('extension') ? ' is-invalid' : ''), 'id' => 'media-extension', 'placeholder' => 'すべて']) }}
                        @if ($errors->has('extension'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('extension') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>登録日（から）</label>
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
                        <label>登録日（まで）</label>
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
                        <label>ステータス</label>
                        {{ Form::select('is_done', $MediaClass::getDones(), Request::get('is_done'),
                        ['class' => 'form-control search-form-control' . ($errors->has('is_done') ? ' is-invalid' : ''), 'id' => 'media-is_done', 'placeholder' => 'すべて']) }}
                        @if ($errors->has('is_done'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('is_done') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>イベント名</label>
                        {{ Form::text('event', Request::get('event'),
                        ['class' => 'form-control search-form-control' . ($errors->has('event') ? ' is-invalid' : ''), 'id' => 'tag-event']) }}
                        @if ($errors->has('event'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('event') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>クラブ名</label>
{{--                        {{ Form::select('name', $clubs, Request::get('name'),--}}
{{--                        ['class' => 'form-control search-form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'id' => 'tag-name']) }}--}}
                        @php
                        $old = intval(Request::get('name'));
                        @endphp
                        <select name="name" id="tag-name" class="form-control search-form-control @error('name') is-invalid @enderror">
                            {{-- クラブアカウントでログインしている場合、クラブ名の数は一つのみ表示するので「すべて」の選択肢はいらない --}}
                            @if (count($clubs) !== 1)
                                <option value="" @if($old=='') selected @endif>すべて</option>
                            @endif
                            @foreach($clubs as $club)
                            <option value="{{$club->id}}" @if($old==$club->id) selected @endif>
                                {{$club->name}}
                            </option>
                            @endforeach
                        </select>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>選手名</label>
                        {{--                        {{ Form::text('players', Request::get('players'),--}}
                        {{--                        ['class' => 'form-control search-form-control' . ($errors->has('players') ? ' is-invalid' : ''), 'id' => 'tag-players']) }}--}}
                        {{ Form::select('players', [], Request::get('players'),
                        ['class' => 'form-control search-form-control ' . ($errors->has('players') ? ' is-invalid' : ''), 'id' => 'tag-players', 'placeholder' => 'すべて']) }}
                        @error('players')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>動画の種類</label>
                        {{ Form::select('movie_type', ['Shot' => 'Shot', 'Goal' => 'Goal', 'Save' => 'Save', 'Assist' => 'Assist'], Request::get('movie_type'),
                        ['class' => 'form-control search-form-control' . ($errors->has('event') ? ' is-invalid' : ''), 'id' => 'movie_type', 'placeholder' => 'すべて']) }}
                        @error('movie_type')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>試合名</label>
                        {{ Form::text('game', Request::get('game'),
                        ['class' => 'form-control search-form-control' . ($errors->has('game') ? ' is-invalid' : ''), 'id' => 'tag-game']) }}
                        @if ($errors->has('game'))
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('game') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>試合日（から）</label>
                        {{ Form::date('game_start', Request::get('game_start'),
                        ['class' => 'form-control search-form-control' . ($errors->has('game_start') ? ' is-invalid' : ''), 'id' => 'tag-game_start']) }}
                        @if ($errors->has('game_start'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('game_start') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>試合日（まで）</label>
                        {{ Form::date('game_end', Request::get('game_end'),
                        ['class' => 'form-control search-form-control' . ($errors->has('game_end') ? ' is-invalid' : ''), 'id' => 'tag-game_end']) }}
                        @if ($errors->has('game_end'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('game_end') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>ブランクの対象</label>
                        {{ Form::select('has_blank', [1 => 'ブランクの対象', 2 => '選手名', 3 => 'クラブ名'], Request::get('has_blank'),
                        ['class' => 'form-control search-form-control' . ($errors->has('has_blank') ? ' is-invalid' : ''), 'id' => 'tag-has_blank', 'placeholder' => 'すべて']) }}
                        @if ($errors->has('has_blank'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('has_blank') }}</strong>
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

    {!! Form::hidden('sort_option1', Request::get('sort_option1')) !!}
    {!! Form::hidden('sort_option2', Request::get('sort_option2')) !!}
    {!! Form::hidden('sort_option3', Request::get('sort_option3')) !!}
    {!! Form::hidden('sort_option_value1', Request::get('sort_option_value1')) !!}
    {!! Form::hidden('sort_option_value2', Request::get('sort_option_value2')) !!}
    {!! Form::hidden('sort_option_value3', Request::get('sort_option_value3')) !!}
    {!! Form::close() !!}

    <!-- /.card -->
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 12px">
                <div class="col-md-12 d-flex" style="justify-content: flex-end;">
                    <div style="margin-right: 20px;">
                        <a href="javascript:void(0);" class="btn btn-warning" data-toggle="modal" data-target="#modal-import">CSVアップロード</a>
                    </div>

                    @if ($medias->total() > 0)
                        {!! Form::open(['route' => 'admin.medias.export', 'method' => 'POST']) !!}
                        {!! Form::hidden('extension', Request::get('extension')) !!}
                        {!! Form::hidden('created_start', Request::get('created_start')) !!}
                        {!! Form::hidden('created_end', Request::get('created_end')) !!}
                        {!! Form::hidden('game_start', Request::get('game_start')) !!}
                        {!! Form::hidden('game_end', Request::get('game_end')) !!}
                        {!! Form::hidden('has_blank', Request::get('has_blank')) !!}
                        {!! Form::hidden('is_done', Request::get('is_done')) !!}
                        {!! Form::hidden('event', Request::get('event')) !!}
                        {!! Form::hidden('players', Request::get('players')) !!}
                        {!! Form::hidden('game', Request::get('game')) !!}
                        {!! Form::hidden('name', Request::get('name')) !!}
                        {!! Form::hidden('sort_option1', Request::get('sort_option1')) !!}
                        {!! Form::hidden('sort_option2', Request::get('sort_option2')) !!}
                        {!! Form::hidden('sort_option3', Request::get('sort_option3')) !!}
                        {!! Form::hidden('sort_option_value1', Request::get('sort_option_value1')) !!}
                        {!! Form::hidden('sort_option_value2', Request::get('sort_option_value2')) !!}
                        {!! Form::hidden('sort_option_value3', Request::get('sort_option_value3')) !!}
                        {{ Form::submit('CSVダウンロード', ['class' => 'btn btn-success']) }}
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        @if ($medias->total() === 0)
            <div class="row">
                <div class="col-12">
                    素材はございません。
                </div>
            </div>
        @else
            <div class="row" style="margin-bottom: 12px">
                <div class="col-md-12 d-flex" style="justify-content: flex-end;">
                    <div style="margin-right: 20px;">
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#modal-sort" class="btn btn-primary">
                            ソート{{ $sortOptions !== '' ? $sortOptions : ''}}
                        </a>
                    </div>
                    <div style="line-height: 40px;">
                        件数：{{ ($medias->currentPage() - 1) * $medias->perPage() + 1 }} ~ {{ (($medias->currentPage()) * $medias->perPage() < $medias->total() ? ($medias->currentPage()) * $medias->perPage() : $medias->total()) }} / {{ $medias->total() }}件
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="example2" class="table table-bordered table-hover medias">
                        <thead>
                            <tr>
                                <th>サムネ</th>
                                <th style="width: 80px;">分類</th>
                                <th style="max-width: 250px;">イベント名</th>
                                <th style="max-width: 200px;">クラブ名</th>
                                <th style="max-width: 200px;">選手名</th>
                                <th style="width: 120px;">更新日</th>
                                <th style="width: 120px;">登録日</th>
                                <th></th>
                                <th style="width: 100px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($medias as $media)
                            <tr>
                                <td class="thumbnail" data-source="{{ $MediaClass::getStaticSourceUrl($media) }}" data-type="{{ $media->extension }}" data-id="{{ $media->id }}">
                                    <img src="{{ $MediaClass::getStaticThumbUrl($media) }}" alt="{{$media->filename}}" />
                                    @if ($media->extension === 'mp4')
                                    <img src="/images/video.png" class="video_play" alt="video_play" />
                                    @endif
                                </td>
                                <td style="width: 80px;">{{ $media->extension === 'jpg' ? '静止画' : '動画' }}</td>
                                <td style="max-width: 250px;">
                                    <a href="{{ route('admin.medias.edit', ['id' => $media->id]) }}" style="display: inline-block;height: 20px;line-height: 20px; border: 0px;overflow: hidden;" >{{ substr($media->event, 0, 100) }}</a>
                                </td>
                                <td style="max-width: 200px;"><span style="display: inline-block;height: 20px;line-height: 20px; border: 0px;overflow: hidden;">{{ $media->name ? substr($media->name, 0, 100) : '' }}</span></td>
                                <td style="max-width: 200px;"><span style="display: inline-block;height: 20px;line-height: 20px; border: 0px;overflow: hidden;">{{ $media->players ? substr($media->players, 0, 100) : '' }}</span></td>
                                <td style="width: 120px;">{{ $Carbon::parse($media->updated_at)->format('Y/m/d H:i') }}</td>
                                <td style="width: 120px;">{{ $Carbon::parse($media->uploaded_at)->format('Y/m/d H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $media->is_done === Media::NOT_DONE ? 'danger' : 'success' }}">{{ $MediaClass::getDones()[$media->is_done] }}</span>
                                </td>
                                <td class="text-right d-flex justify-content-around" style="width: 100px;">
                                    @if ($media->is_show === Media::NOT_SHOW)
                                        <button class="btn btn-sm btn-danger btn-toggle-show" data-id="{{ $media->id }}" data-label="非表示">非表示</button>
                                    @else
                                        <button class="btn btn-sm btn-success btn-remove" data-id="{{ $media->id }}" data-label="表示">表示</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {!! $medias->appends(Request::except('_token'))->links() !!}
        @endif
        </div>
        
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <span style="display: none;" id="openModal" data-toggle="modal" data-target="#modal-image"></span>
    <span style="display: none;" data-toggle="modal" data-target="#modal-delete" id="deleteModal"></span>
    <span style="display: none;" data-toggle="modal" data-target="#modal-toggle-show" id="toggleShowModal"></span>

    <div class="modal fade" id="modal-image">
        <div class="modal-dialog modal-image">
            <div class="modal-content">
                <div class="modal-body" data-dismiss="modal">
                    <img src="" alt="" id="imgModal" />
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'admin.medias.toggle', 'method' => 'POST']) !!}
                    {!! Form::hidden('id', '', ['id' => 'media_id']) !!}
                <div class="modal-header">
                    素材の非表示
                </div>
                <div class="modal-body">
                    この素材を非表示しますか？
                </div>
                <div class="modal-footer">
                    {!! Form::submit('非表示する', ['class' => 'btn btn-danger']) !!}
                    <button class="btn btn-gray" data-dismiss="modal">キャンセル</button>
                </div>
                {!! Form::close() !!}
            </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modal-toggle-show">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'admin.medias.toggle', 'method' => 'POST']) !!}
                    {!! Form::hidden('id', '', ['id' => 'toggle_media_id']) !!}
                <div class="modal-header">
                    素材の表示
                </div>
                <div class="modal-body">
                    この素材を表示しますか？
                </div>
                <div class="modal-footer">
                    {!! Form::submit('表示する', ['class' => 'btn btn-success']) !!}
                    <button class="btn btn-gray" data-dismiss="modal">キャンセル</button>
                </div>
                {!! Form::close() !!}
            </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modal-import">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'admin.medias.import', 'method' => 'POST', 'id' => 'csvForm', 'enctype' => 'multipart/form-data']) !!}
                <div class="modal-header">
                    アップロードの確認
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

    <div class="modal fade" id="modal-sort">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'admin.medias.index', 'method' => 'GET']) !!}
                    {!! Form::hidden('extension', Request::get('extension')) !!}
                    {!! Form::hidden('created_start', Request::get('created_start')) !!}
                    {!! Form::hidden('created_end', Request::get('created_end')) !!}
                    {!! Form::hidden('game_start', Request::get('game_start')) !!}
                    {!! Form::hidden('game_end', Request::get('game_end')) !!}
                    {!! Form::hidden('has_blank', Request::get('has_blank')) !!}
                    {!! Form::hidden('is_done', Request::get('is_done')) !!}
                    {!! Form::hidden('event', Request::get('event')) !!}
                    {!! Form::hidden('players', Request::get('players')) !!}
                    {!! Form::hidden('game', Request::get('game')) !!}
                    {!! Form::hidden('name', Request::get('name')) !!}
                    <div class="modal-header">
                        ソート項目
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label>ソート項目01</label>
                            </div>
                            <div class="col-md-6">
                                {{ Form::select('sort_option1', $MediaClass::getSortOptions(), Request::get('sort_option1'), ['class' => 'form-control sort-form-control', 'id' => 'media-sort_option1', 'placeholder' => 'なし'])}}
                            </div>
                            <div class="col-md-6">
                                {{ Form::select('sort_option_value1', $MediaClass::getSortValueOptions(), Request::get('sort_option_value1'), ['class' => 'form-control sort-value-form-control', 'id' => 'media-sort_option_value1']) }}
                            </div>
                            <div class="col-md-12">
                                <label>ソート項目02</label>
                            </div>
                            <div class="col-md-6">
                                {{ Form::select('sort_option2', $MediaClass::getSortOptions(), Request::get('sort_option2'), ['class' => 'form-control sort-form-control', 'id' => 'media-sort_option2', 'placeholder' => 'なし'])}}
                            </div>
                            <div class="col-md-6">
                                {{ Form::select('sort_option_value2', $MediaClass::getSortValueOptions(), Request::get('sort_option_value2'), ['class' => 'form-control sort-value-form-control', 'id' => 'media-sort_option_value2']) }}
                            </div>
                            <div class="col-md-12">
                                <label>ソート項目03</label>
                            </div>
                            <div class="col-md-6">
                                {{ Form::select('sort_option3', $MediaClass::getSortOptions(), Request::get('sort_option3'), ['class' => 'form-control sort-form-control', 'id' => 'media-sort_option3', 'placeholder' => 'なし'])}}
                            </div>
                            <div class="col-md-6">
                                {{ Form::select('sort_option_value3', $MediaClass::getSortValueOptions(), Request::get('sort_option_value3'), ['class' => 'form-control sort-value-form-control', 'id' => 'media-sort_option_value3']) }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {!! Form::submit('確定する', ['class' => 'btn btn-primary']) !!}
                        <button type="button" class="btn btn-success" id="btnSortReset">リセット</button>
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
    const clubs = {!! json_encode($clubs->toArray()) !!};
    const oldPlayer = '{!! Request::get('players') !!}';

    {{-- クラブのアカウントでログインしている場合、クラブ名の選択肢は一つなのでoldClubの値は確定する --}}
    @if (count($clubs) === 1)
    const oldClub = '{{ $admin->id }}';
    @else
    const oldClub = '{!! Request::get('name') !!}';
    @endif

$(document).ready(function() {
    // load player list
    var clubId = parseInt(oldClub)
    if(isNaN(clubId)) return
    var club = clubs.find( club => club.id === clubId)
    if(club === undefined) return

    $('#tag-players').empty().append(`<option value="" ${oldPlayer === '' ? 'selected' : ''}>すべて</option>`)

    club.players.forEach((p, k) => {
      const selected = oldPlayer == p.id ? 'selected' : ''
      const option = `<option value="${p.id}" ${selected}>${p.name}</option>`
      $('#tag-players').append(option)
    })

})
    $('#tag-name').change(() => {
        const clubId = parseInt($('#tag-name').val())
        if(isNaN(clubId)) return
        const club = clubs.find( club => club.id === clubId)
        if(club === undefined) return

        $('#tag-players').empty().append(`<option value="" ${oldPlayer === '' ? 'selected' : ''}>すべて</option>`)

        club.players.forEach((p, k) => {
            const selected = oldPlayer === p.id ? 'selected' : ''
            const option = `<option value="${p.id}" ${selected}>${p.name}</option>`
            $('#tag-players').append(option)
        })
    })

    $('.thumbnail').click(function() {
        if ($(this).data('type') === 'jpg') {
            $('#imgModal').attr('src', $(this).data('source'));
            $('#openModal').click();
        } else {
            window.location.href = '/admin/medias/edit/' + $(this).data('id');
        }
    });
    $('.btn-remove').click(function() {
        $('#media_id').attr('value', $(this).data('id'));
        $('#deleteModal').click();
    })
    $('.btn-toggle-show').click(function() {
        $('#toggle_media_id').attr('value', $(this).data('id'));
        $('#toggleShowModal').click();
    })

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

    $('#btnReset').click(function() {
        $('.search-form-control').map(function() {
            $(this).val('');
        })
        $('#search-form').submit()
    })
    $('#btnSortReset').click(function() {
        $('.sort-form-control').map(function() {
            $(this).val('');
        })
        $('.sort-value-form-control').map(function() {
            $(this).val('asc');
        })
    })

    $('#modal-import').on('hidden.bs.modal', function() {
        $('#selectedFile').text('');
        $('#fileAlert').addClass('hidden');
        $('#btnCSVImport').attr('disabled', 'disabled');
        $('#csv_file').val('');
    })
</script>
@stop
