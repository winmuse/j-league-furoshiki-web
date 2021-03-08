<?php

namespace App\Http\Controllers\Admin\AWS;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\AWS\AWSService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AWSController extends Controller
{
    /**
     * @var AWSService
     */
    private $service;

    /**
     * AWSController constructor
     *
     * @param AWSService $service
     */
    public function __construct(
        AWSService $service
    )
    {
        $this->service = $service;

        $this->middleware('admin.auth:admin');
    }

    /**
     *
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $attributes = $request->all();

        // クラブのアカウントだった場合は、そのクラブに属する選手のみ検索できる
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        $isClub = $admin->role === Admin::CLUB_ROLE;
        $clubId = $isClub ? $admin->id : null;
        if ($isClub) {
            $attributes['name'] = $admin->id;
        }

        $clubs = $this->service->getClubs($clubId);
        $medias = $this->service->search($attributes);

        $sortOptions = $this->service->getSortOptionString($attributes);

        $request->session()->put('media_search', $attributes);

        return view('admin.medias.index', compact('admin', 'clubs', 'medias', 'sortOptions'));
    }

    public function test() {
      return $this->service->getClubs();
    }

    /**
     * 編集ぺーじ
     *
     * @param int $id
     *
     * @return View
     */
    public function edit($id): View
    {
        $media = $this->service->getMedia($id);

        return view('admin.medias.edit', compact('media'));
    }

    /**
     * Update
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var Media $media */
        $media = $this->service->getMedia(intval(request('id')));

        \DB::beginTransaction();
        try {
            $this->service->updateMedia($media, $request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.medias.edit', ['id' => $media->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.medias.edit', ['id' => $media->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => '素材を変更できません']);
        }

        \DB::commit();

        $params = ['is_done' => 0];
        if (!is_null($request->session()->get('media_search'))) {
            $params = $request->session()->get('media_search');
        }

        return redirect()->route('admin.medias.index', $params)
                ->with(['system.message.success' => '素材を保存しました。']);
    }

    /**
     * 削除
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $this->service->deleteMedia(request('id'));

        $params = ['is_done' => 0];
        if (!is_null($request->session()->get('media_search'))) {
            $params = $request->session()->get('media_search');
        }

        return redirect()->route('admin.medias.index', $params)
                ->with(['system.message.success' => '素材を削除しました。']);
    }

    /**
     * 表示／非表示
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function toggle(Request $request): RedirectResponse
    {
        $this->service->toggleMedia(request('id'));

        $params = ['is_done' => 0];
        if (!is_null($request->session()->get('media_search'))) {
            $params = $request->session()->get('media_search');
        }

        return redirect()->route('admin.medias.index', $params)
                ->with(['system.message.success' => '変更しました。']);
    }

    /**
     * ダウンロード
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        $exports = new \App\Exports\MediasExport();
        $exports->medias = $this->service->searchForExport($request->all());

        return Excel::download($exports, "medias.xlsx");
    }

    /**
     * アップロード
     *
     * @param Request $request
     */
    public function import()
    {
        \DB::beginTransaction();

        try {
            Excel::import(new \App\Imports\MediasImport, request()->file('csv_file'));
        } catch (\Throwable $e) {
            \DB::rollBack();
            if ($e->getMessage() === 'invalid file') {
                return back()->with(['system.message.danger' => '正しいデータをアップロードしてください。']);
            } else if ($e->getMessage() === 'invalid dateformat') {
                return back()->with(['system.message.danger' => '「撮影日」の項目は「YYYY-MM-DD」形で入力してください。(例：2020-01-01)']);
            } else if ($e->getMessage() === 'invalid gametime') {
                return back()->with(['system.message.danger' => '「昼／夜」の項目は「昼」や「夜」を入力してください。']);
            } else if ($e->getMessage() === 'invalid item') {
                return back()->with(['system.message.danger' => '「ID」、「クレジット」、「イベント名」は変更できません。']);
            } else if ($e->getMessage() === 'invalid status') {
                return back()->with(['system.message.danger' => '「ステータス」の項目は「0」や「1」を入力してください。(0:未完了, 1:完了)']);
            } else if ($e->getMessage() === 'invalid top') {
                return back()->with(['system.message.danger' => '「検索上位表示」の項目は「0」や「1」を入力してください。(0: 表示しない, 1:表示する)']);
            }

            return back()->with(['system.message.danger' => '正しないデータがあります。']);
        }

        \DB::commit();

        return back()->with(['system.message.success' => '素材データが更新されました。']);
    }
}
