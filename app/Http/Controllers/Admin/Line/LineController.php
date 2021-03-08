<?php

namespace App\Http\Controllers\Admin\Line;

use App\Http\Controllers\Controller;
use App\Models\LineCredential;
use App\Services\Admin\Line\LineService;
use Illuminate\View\View;
use App\Http\Requests\Line\IndexGet;
use App\Http\Requests\Line\UpdatePut;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LineController extends Controller
{
    /**
     * @var LineService
     */
    private $service;

    /**
     * TagController constructor
     *
     * @param LineService $lineService
     */
    public function __construct(
        LineService $lineService
    )
    {
        $this->service = $lineService;

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
        $lines = $this->service->search($request->validated());

        return view('admin.lines.index', compact('lines'));
    }

    /**
     * 編集ページ
     *
     * @param int $id
     *
     * @return View
     */
    public function edit($id): View
    {
        $line = $this->service->getLine($id);

        return view('admin.lines.edit', compact('line'));
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
        /** @var LineCredential $line */
        $line = $this->service->getLine(intval(request('id')));

        \DB::beginTransaction();

        try {
            $this->service->updateLine($line, $request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.lines.edit', ['id' => $line->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.tags.edit', ['id' => $line->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'LINEアカウントを変更できません']);
        }

        \DB::commit();

        return redirect()->route('admin.lines.index')
                ->with(['system.message.success' => 'LINEアカウントを保存しました。']);
    }
}
