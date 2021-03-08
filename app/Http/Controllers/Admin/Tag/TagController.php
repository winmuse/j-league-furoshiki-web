<?php

namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Tag\TagService;
use Illuminate\View\View;
use App\Http\Requests\Tag\IndexGet;
use App\Http\Requests\Tag\UpdatePut;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TagController extends Controller
{
    /**
     * @var TagService
     */
    private $service;

    /**
     * TagController constructor
     *
     * @param TagService $tagService
     */
    public function __construct(
        TagService $tagService
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

        $request->session()->put('tag_search', $request->all());

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Create Form
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.tags.create');
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

        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update
     *
     * @param UpdatePut $request
     *
     * @return RedirectResponse
     */
    public function update(UpdatePut $request): RedirectResponse
    {
        /** @var Tag $tag */
        $tag = $this->service->getTag(intval(request('id')));

        \DB::beginTransaction();

        try {
            $this->service->updateTag($tag, $request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.tags.edit', ['id' => $tag->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.tags.edit', ['id' => $tag->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'ハッシュタグを変更できません']);
        }

        \DB::commit();

        $params = [];
        if (!is_null($request->session()->get('tag_search'))) {
            $params = $request->session()->get('tag_search');
        }

        return redirect()->route('admin.tags.index', $params)
                ->with(['system.message.success' => 'ハッシュタグを保存しました。']);
    }

    /**
     * Store
     *
     * @param UpdatePut $request
     *
     * @return RedirectResponse
     *
     */
    public function store(UpdatePut $request): RedirectResponse
    {
        \DB::beginTransaction();

        try {
            $this->service->createTag($request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.tags.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.tags.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'ハッシュタグを登録できません']);
        }

        \DB::commit();

        return redirect()->route('admin.tags.index')
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
        if (!is_null($request->session()->get('tag_search'))) {
            $params = $request->session()->get('tag_search');
        }

        return redirect()->route('admin.tags.index', $params)
                ->with(['system.message.success' => 'ハッシュタグを削除しました。']);
    }
}
