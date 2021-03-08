<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Media;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MediasExport implements FromView, ShouldAutoSize
{
    public $medias;

    public function view(): View
    {
        return view('csv.media', [
            'medias' => $this->medias
        ]);
    }
}