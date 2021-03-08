<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaAWSMeta;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Media;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class MediaController extends Controller
{
    /**
     * get media List of club
     * @param Request $request
     * @return JsonResponse : media list
     * @throw
     */
    public function getMediaList(Request $request)
    {
        $user = JWTAuth::toUser($request->token);
        if (is_null($user)) {
            return response()->json([
                'error' => 'not authenticated'
            ], 400);
        }
        $clubId = $user->profile->admin_id;
        $medias = DB::table('medias as m')
            ->leftJoin('medias_aws_meta as t', 'm.id', '=', 't.media_id')
            ->where('m.club_id', '=', $clubId)
            ->where('m.is_show', '=', 1)
            ->where(function ($query) {
                $query->where('m.extension', 'jpg')
                    ->orWhere(function ($q) {
                        $q->where('m.extension', 'mp4')
                            ->where('m.is_done', 1);

                        // 動画は取り込み日の24時以降でなければ公開しない (本番のみ)
                        if (config('app.env') === 'production') {
                            $q->where('m.created_at', '<', Carbon::now()->format('Y-m-d 00:00:00'));
                        }
                    });
            })
            ->orderByRaw('CONCAT(t.game_date, "_", m.is_top) DESC')
            ->get();
        $result = [];

        foreach ($medias as $m) {
            array_push(
                $result,
                Media::with('meta')->find($m->id)
            );
        }
        return response()->json($result, 200);
    }

    /**
     * search media List of club
     * @param Request $request
     * @return JsonResponse : media list
     * @throw
     */
    public function searchMediaList(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'not authenticated'
            ], 400);
        }
        $userId = Auth::id();
        // $clubId = User::find($userId)->profile->admin_id;
        $playerId = intval($request->get('opt_player', '0'));
        $keyword = $request->get('opt_keyword', null);
        $gameDate = $request->get('opt_date', null);
        $keywordCheck = !is_null($keyword) && $keyword !== '';
        $dateCheck = !is_null($gameDate) && $gameDate !== '';
        $page = $request->get('opt_page', 0);
        $pageSize = $request->get('opt_size', 30);
        $offset = $page * $pageSize;

        $like = '%' . $keyword . '%';
        $medias = DB::table('medias as m')
            ->leftJoin('medias_aws_meta as t', 'm.id', '=', 't.media_id')
            // ->where('m.club_id', '=', $clubId)
            ->where('m.is_show', '=', 1)
            ->where(function ($query) {
                $query->where('m.extension', 'jpg')
                    ->orWhere(function ($q) {
                        $q->where('m.extension', 'mp4')
                            ->where('m.is_done', 1);

                        // 動画は取り込み日の24時以降でなければ公開しない (本番のみ)
                        if (config('app.env') === 'production') {
                            $q->where('m.created_at', '<', Carbon::now()->format('Y-m-d 00:00:00'));
                        }
                    });
            });

        if ($keywordCheck) {
            $medias = $medias->where(function ($qry) use ($like) {
                $qry->where('t.event', 'like', $like)
                    ->orWhere('t.game', 'like', $like)
                    ->orWhere('t.game_place', 'like', $like)
                    ->orWhere('t.home_team', 'like', $like)
                    ->orWhere('t.away_team', 'like', $like)
                    ->orWhere('t.subject1', 'like', $like)
                    ->orWhere('t.subject2', 'like', $like)
                    ->orWhere('t.subject3', 'like', $like)
                    ->orWhere('t.state1', 'like', $like)
                    ->orWhere('t.state2', 'like', $like)
                    ->orWhere('t.state3', 'like', $like)
                    ->orWhere('t.others', 'like', $like)
                    ->orWhere('t.group_name', 'like', $like);
            });
        }

        if ($dateCheck) {
            $medias = $medias->whereDate('t.game_date', '=', $gameDate);
        }

        if ($playerId !== 0) {
            // 検索で選手名が指定されている場合
            $specifiedUser = User::find($playerId);
            if (!is_null($specifiedUser)) {
                $medias = $medias->where(function ($qry) use ($specifiedUser) {
                    $jname = str_replace('　', '', $specifiedUser->name);
                    $jname = str_replace(' ', '', $jname);
                    $ename = $specifiedUser->name_en;
                    // $clubName = User::find($userId)->profile->admin->name;
                    // $playerName = User::find($userId)->name;
                    // $playerNameEn = User::find($userId)->name_en;
                    $qry->whereRaw("FIND_IN_SET('{$jname}', t.players)");
                    $qry->orWhereRaw("FIND_IN_SET('{$ename}', t.players)");
                });
            }
        } else {
            // 検索で選手名が指定されていない場合
            // [仕様メモ]
            // 選手名欄に、「選手名」が入っていればその選手が所属するチームの全員のページで表示される
            //「チーム名」をいれておけば、同チームの全選手のページ人表示される
            // → 選手名欄に、ログインしている選手が属しているチームの「メンバー全員のそれぞれの名前」か「チーム名」が含まれている素材を取得する
            $medias = $medias->where(function ($qry) use ($userId) {
                $user = User::find($userId);

                $teamMemberUserIds = Profile::where('admin_id', $user->profile->admin_id)->get()->pluck('user_id');
                $teamMemberUsers = User::query()->whereIn('id', $teamMemberUserIds)->get();

                $clubName = $user->profile->admin->name;
                $qry->orWhereRaw("FIND_IN_SET('{$clubName}', t.players)");

                foreach ($teamMemberUsers as $teamMember) {
                    $playerName = $teamMember->name;
                    $playerNameEn = $teamMember->name_en;

                    $qry->orWhereRaw("FIND_IN_SET('{$playerName}', t.players)");
                    $qry->orWhereRaw("FIND_IN_SET('{$playerNameEn}', t.players)");
                }
            });
        }

        $medias = $medias
            ->orderByRaw('CONCAT(t.game_date, "_", m.is_top) DESC')
            ->offset($offset)
            ->limit($pageSize)
            ->get();
        $result = [];

        foreach ($medias as $m) {
            array_push(
                $result,
                Media::with('meta')->find($m->id)
            );
        }

        return response()->json($result, 200);
    }

    /**
     * get List of club
     * @return JsonResponse : club list
     * @throw
     */
    public function getClubList()
    {
        $clubs = Admin::where('role', 'club')->get();
        return response()->json($clubs, 200);
    }

    /**
     * get List of players in club
     * @return JsonResponse : player list
     * @throw
     */
    public function getPlayerList()
    {
        $userId = Auth::id();
        $clubId = User::find($userId)->profile->admin_id;
        $players = Profile::with('user')
            ->has('user')
            ->where('admin_id', $clubId)
            ->get();
        return response()->json($players, 200);
    }
}
