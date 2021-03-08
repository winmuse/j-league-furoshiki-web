<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Account\AccountService;
use Illuminate\View\View;
use App\Http\Requests\Account\IndexGet;
use App\Http\Requests\Account\UpdatePut;
use App\Http\Requests\Account\InsertPut;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;

class AccountController extends Controller
{
    /**
     * @var AccountService
     */
    private $service;

    /**
     * AccountController constructor
     * 
     * @param AccountService $accountService
     */
    public function __construct(
        AccountService $accountService
    )
    {
        $this->service = $accountService;

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
        $accounts = $this->service->search($request->validated());

        $clubs = [];
        if (auth()->user()->role === \App\Models\Admin::JLEAGUE_ROLE) {
            $clubs = $this->service->getClubs();
        }

        $request->session()->put('account_search', $request->all());

        return view('admin.accounts.index', compact('accounts', 'clubs'));
    }

    /**
     * Create Form
     * 
     * @return View
     */
    public function create(): View
    {
        $clubs = [];
        if (auth()->user()->role === \App\Models\Admin::JLEAGUE_ROLE) {
            $clubs = $this->service->getClubs();
        }

        return view('admin.accounts.create', compact('clubs'));
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
        $account = $this->service->getAccount($id);

        $clubs = [];
        if (auth()->user()->role === \App\Models\Admin::JLEAGUE_ROLE) {
            $clubs = $this->service->getClubs();
        }

        return view('admin.accounts.edit', compact('account', 'clubs'));
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
        /** @var User $user */
        $user = $this->service->getAccount(intval(request('id')));

        \DB::beginTransaction();

        try {
            $this->service->updateAccount($user, $request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.accounts.edit', ['id' => $user->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.accounts.edit', ['id' => $user->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'アカウント情報を変更できません']);
        }

        \DB::commit();

        $params = [];
        if (!is_null($request->session()->get('account_search'))) {
            $params = $request->session()->get('account_search');
        }
        
        return redirect()->route('admin.accounts.index', $params)
                ->with(['system.message.success' => 'アカウントの情報を保存しました。']);
    }

    /**
     * Store
     * 
     * @param InsertPut $request
     * 
     * @return RedirectResponse
     * 
     */
    public function store(InsertPut $request): RedirectResponse
    {
        /** @var User $user */
        \DB::beginTransaction();

        try {
            $this->service->createAccount($request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.accounts.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.accounts.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'アカウント情報を登録できません']);
        }

        \DB::commit();
        
        return redirect()->route('admin.accounts.index')
                ->with(['system.message.success' => 'アカウントを登録しました。']);
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
        $this->service->deleteAccount(request('id'));

        $params = [];
        if (!is_null($request->session()->get('account_search'))) {
            $params = $request->session()->get('account_search');
        }

        return redirect()->route('admin.accounts.index', $params)
                ->with(['system.message.success' => 'アカウントは削除しました。']);
    }

    /**
     * ダウンロード
     *
     */
    public function exportTemplate()
    {
        $exports = new \App\Exports\AccountsTemplateExport();

        return Excel::download($exports, "template.xlsx");
    }

    /**
     * ダウンロード
     *
     */
    public function exportAllAccounts()
    {
        $exports = new \App\Exports\AllAccountsExport();

        return Excel::download($exports, "all_accounts.xlsx");
    }

    /**
     * アップロード
     *
     * @param Request $request
     */
    public function import()
    {
        try {
	    ini_set('max_execution_time', 300);
            Excel::import(new \App\Imports\AccountsImport, request()->file('csv_file'));
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            return back()->with(['system.message.danger' => '正しいデータをアップロードしてください。']);
        }

        return back()->with(['system.message.success' => '選手データが更新されました。']);
    }
}
