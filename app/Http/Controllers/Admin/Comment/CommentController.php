<?php

namespace App\Http\Controllers\Admin\Comment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Comment\CommentService;
use Illuminate\View\View;
use App\Http\Requests\Comment\IndexGet;
use App\Http\Requests\Comment\UpdatePut;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CommentController extends Controller
{
    /**
     * @var CommentService
     */
    private $service;

    /**
     * CommentController constructor
     *
     * @param CommentService $commentService
     */
    public function __construct(
        CommentService $commentService
    )
    {
        $this->service = $commentService;

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
        $comments = $this->service->search($request->validated());

        $request->session()->put('comment_search', $request->all());

        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Create Form
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.comments.create');
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
        $comment = $this->service->getComment($id);

        return view('admin.comments.edit', compact('comment'));
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
        /** @var Comment $comment */
        $comment = $this->service->getComment(intval(request('id')));

        \DB::beginTransaction();

        try {
            $this->service->updateComment($comment, $request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.comments.edit', ['id' => $comment->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.comments.edit', ['id' => $comment->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'コメントを変更できません']);
        }

        \DB::commit();

        $params = [];
        if (!is_null($request->session()->get('comment_search'))) {
            $params = $request->session()->get('comment_search');
        }

        return redirect()->route('admin.comments.index', $params)
                ->with(['system.message.success' => 'コメントを保存しました。']);
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
            $this->service->createComment($request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.comments.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.comments.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'コメントを登録できません']);
        }

        \DB::commit();

        return redirect()->route('admin.comments.index')
                ->with(['system.message.success' => 'コメントを登録しました。']);
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
        $this->service->deleteComment(request('id'));

        $params = [];
        if (!is_null($request->session()->get('comment_search'))) {
            $params = $request->session()->get('comment_search');
        }

        return redirect()->route('admin.comments.index', $params)
                ->with(['system.message.success' => 'コメントを削除しました。']);
    }
}
