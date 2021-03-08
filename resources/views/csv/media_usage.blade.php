@inject('AdminClass', 'App\Models\Admin')
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
        <th>利用した選手名</th>
        <th>利用した内容</th>
        <th>SNS_URLS</th>
    </tr>
    </thead>
    <tbody>
    @foreach($medias as $media)
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
            <tr>
                <td>{{ $media->id }}</td>
                <td>{{ $media->creator }}</td>
                <td>{{ $media->meta->event }}</td>
                <td>{{ $media->meta->game }}</td>
                <td>{{ $media->meta->game_place }}</td>
                <td>{{ $media->meta->game_date }}</td>
                <td>{{ $media->meta->game_time }}</td>
                <td>{{ $media->meta->home_team }}</td>
                <td>{{ $media->meta->away_team }}</td>
                <td>{{ $media->meta->players }}</td>
                <td>{{ $media->meta->subject1 }}</td>
                <td>{{ $media->meta->subject2 }}</td>
                <td>{{ $media->meta->subject3 }}</td>
                <td>{{ $media->meta->state1 }}</td>
                <td>{{ $media->meta->state2 }}</td>
                <td>{{ $media->meta->state3 }}</td>
                <td>{{ $media->meta->group_name }}</td>
                <td>{{ $article->user->name }}</td>
                <td>{{ $article->description }}</td>
                @foreach ($article->sns_urls as $sns)
                    <td>{{ $sns->url }}</td>
                @endforeach
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>