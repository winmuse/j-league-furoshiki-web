<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\MediaUsage;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MediaUsagesExport implements FromView, ShouldAutoSize //, WithHeadings
{
    public $mediaUsages;

    public function view(): View
    {
        return view('csv.media_usage', [
            'medias' => $this->mediaUsages
        ]);
    }
}
