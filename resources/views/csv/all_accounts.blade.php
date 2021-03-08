@php
/** User[] $users */
@endphp

<table>
    <thead>
    <tr>
        <th>選手番号</th>
        <th>チーム名漢字</th>
        <th>チーム名英字</th>
        <th>選手氏名漢字</th>
        <th>選手氏名英字</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->player_no ?? '' }}</td>
                <td>{{ $user->profile->admin->name ?? '' }}</td>
                <td>{{ $user->profile->admin->name_en ?? '' }}</td>
                <td>{{ $user->name ?? '' }}</td>
                <td>{{ $user->name_en ?? '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>