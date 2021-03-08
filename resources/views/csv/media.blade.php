<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>クレジット</th>
        <th>イベント名</th>
        <th>試合名</th>
        <th>撮影場所</th>
        <th>撮影日</th>
        <th>昼／夜</th>
        <th>チーム名(ホーム)</th>
        <th>チーム名(アウェイ)</th>
        <th>選手名</th>
        <th>被写体1</th>
        <th>被写体2</th>
        <th>被写体3</th>
        <th>状態1</th>
        <th>状態2</th>
        <th>状態3</th>
        <th>グループ</th>
        <th>ステータス(0:未完了, 1:完了)</th>
        <th>検索上位表示(0: 表示しない, 1:表示する)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($medias as $media)
        <tr>
            <td>{{ $media->id }}</td>
            <td>{{ $media->creator }}</td>
            <td>{{ $media->event }}</td>
            <td>{{ $media->game }}</td>
            <td>{{ $media->game_place }}</td>
            <td>{{ $media->game_date }}</td>
            <td>{{ $media->game_time }}</td>
            <td>{{ $media->home_team }}</td>
            <td>{{ $media->away_team }}</td>
            <td>{{ $media->players }}</td>
            <td>{{ $media->subject1 }}</td>
            <td>{{ $media->subject2 }}</td>
            <td>{{ $media->subject3 }}</td>
            <td>{{ $media->state1 }}</td>
            <td>{{ $media->state2 }}</td>
            <td>{{ $media->state3 }}</td>
            <td>{{ $media->group_name }}</td>
            <td>{{ $media->is_done }}</td>
            <td>{{ $media->is_top }}</td>
        </tr>
    @endforeach
    </tbody>
</table>