<?php

namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Tag\ExpireTagService;
use Illuminate\View\View;
use App\Http\Requests\Tag\IndexGet;
use App\Http\Requests\Tag\ExpireUpdatePut;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ExpireTagController extends Controller
{
    /**
     * @var ExpireTagService
     */
    private $service;

    /**
     * TagController constructor
     * 
     * @param ExpireTagService $tagService
     */
    public function __construct(
        ExpireTagService $tagService
    )
    {
        $this->service = $tagService;

        $this->middleware('admin.auth:admin');
    }

    /**
     * 
     * @param IndexGet $request
     * 
     * @return View
     */
    public function index(IndexGet $request): View
    {
        $tags = $this->service->search($request->validated());

        $request->session()->put('expire_tag_search', $request->all());

        return view('admin.expire.tags.index', compact('tags'));
    }

    /**
     * Create Form
     * 
     * @return View
     */
    public function create(): View
    {
        return view('admin.expire.tags.create');
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
        $tag = $this->service->getTag($id);

        return view('admin.expire.tags.edit', compact('tag'));
    }

    /**
     * Update
     * 
     * @param ExpireUpdatePut $request
     * 
     * @return RedirectResponse
     */
    public function update(ExpireUpdatePut $request): RedirectResponse
    {
        /** @var ExpireTag $tag */
        $tag = $this->service->getTag(intval(request('id')));

        \DB::beginTransaction();

        try {
            $this->service->updateTag($tag, $request->all());
        } catch (\LogicException $e) {
//            logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.expire.tags.edit', ['id' => $tag->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
//            logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.expire.tags.edit', ['id' => $tag->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'ハッシュタグを変更できません']);
        }

        \DB::commit();

        $params = [];
        if (!is_null($request->session()->get('expire_tag_search'))) {
            $params = $request->session()->get('expire_tag_search');
        }
        
        return redirect()->route('admin.expire.tags.index', $params)
                ->with(['system.message.success' => 'ハッシュタグを保存しました。']);
    }

    /**
     * Store
     * 
     * @param ExpireUpdatePut $request
     * 
     * @return RedirectResponse
     * 
     */
    public function store(ExpireUpdatePut $request): RedirectResponse
    {
        \DB::beginTransaction();

        try {
            $this->service->createTag($request->all());
        } catch (\LogicException $e) {
//            logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.expire.tags.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.expire.tags.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'ハッシュタグを登録できません']);
        }

        \DB::commit();
        
        return redirect()->route('admin.expire.tags.index')
                ->with(['system.message.success' => 'ハッシュタグを登録しました。']);
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
        $this->service->deleteTag(request('id'));

        $params = [];
        if (!is_null($request->session()->get('expire_tag_search'))) {
            $params = $request->session()->get('expire_tag_search');
        }

        return redirect()->route('admin.expire.tags.index', $params)
                ->with(['system.message.success' => 'ハッシュタグを削除しました。']);
    }
}
