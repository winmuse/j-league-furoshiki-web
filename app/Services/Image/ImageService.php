<?php

namespace App\Services\Image;

use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;
use Intervention\Image\Gd\Font;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImageService
 * @package App\Http\Services\Image
 */
class ImageService
{
    /**
     * @param string $url
     * @param string $teamName
     * @return
     */
    public function getFileContentsWithCredit($url, $teamName)
    {
        $filename = basename($url);
        $tmpname = public_path('tmp/'.$filename);
        if(file_exists($tmpname)) {
            return file_get_contents($tmpname);
        }

        $this->writePhotoCredit($url, $tmpname, $teamName);
        return file_get_contents($tmpname);
    }

    /**
     * @param string $url
     * @param string $teamName
     * @return
     */
    public function getFileUrlWithCredit($url, $teamName)
    {
        $filename = basename($url);
        $tmpname = public_path('tmp/'.$filename);
        if(file_exists($tmpname)) {
            return url('/tmp/'.$filename);
        }

        $this->writePhotoCredit($url, $tmpname, $teamName);
        return url('/tmp/'.$filename);
    }

    private function writePhotoCredit($url, $tmpname, $club) {
        $contents = file_get_contents($url);
        $tmpDir = public_path('tmp');
        if(!file_exists($tmpDir) && !is_dir($tmpDir)) {
            mkdir($tmpDir);
        }
        file_put_contents($tmpname, $contents);
        // write credits
        $new = Image::make($tmpname);

        $credit = $club;
        $new->text(
            $credit,
            $new->width() - 20,
            $new->height() - 20,
            function($font) {
//                $font->file(public_path('/fonts/noto-sans/NotoSans-Regular.ttf'));
                $font->file(public_path('/fonts/honoka-min/font_1_honokamin.ttf'));
                $font->size(32);
                $font->align('right');
                $font->valign('bottom');
                $font->color(array(255, 255, 255, 0.9));
            }
        );
        $new->save($tmpname);
    }
}
