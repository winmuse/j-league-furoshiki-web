<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllAccountsExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        $users = User::query()
            ->whereNotNull('player_no')
            ->with(['profile' => function($query) {
                return $query->with('admin');
            }])
            ->orderBy('id', 'desc')
            ->get();

        return view('csv.all_accounts', [
            'users' => $users,
        ]);
    }
}