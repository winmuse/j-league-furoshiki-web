<?php

namespace App\Imports;

use App\Models\Media;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;

class MediasImport implements ToModel
{
    private $keys = [
        'ID',
        'クレジット',
        'イベント名',
        '試合名',
        '撮影場所',
        '撮影日',
        '昼／夜',
        'チーム名(ホーム)',
        'チーム名(アウェイ)',
        '選手名',
        '被写体1',
        '被写体2',
        '被写体3',
        '状態1',
        '状態2',
        '状態3',
        'グループ',
        'ステータス(0:未完了, 1:完了)',
        '検索上位表示(0: 表示しない, 1:表示する)'
    ];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (sizeof($row) !== 19) {
            throw new \Exception('invalid file');
        }

        if ($row[0] !== 'ID') {
            $media = null;

            if (isset($row[0]) && intval($row[0]) > 0) { // Update
                $media = Media::find($row[0]);
            }

            $metaAttributes = array();

            if (!isset($media)) {
                throw new \Exception('invalid item');
            }

            if ($media->creator !== $row[1] || $media->meta->event !== $row[2]) {
                throw new \Exception('invalid item');
            }

            if (!is_null($row[5]) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $row[5])) {
                throw new \Exception('invalid dateformat');
            }

            if (!is_null($row[6]) && !in_array($row[6], ["昼", "夜"])) {
                throw new \Exception('invalid gametime');
            }

            if (!is_numeric($row[17]) || (intval($row[17]) !== 0 && intval($row[17]) !== 1)) {
                throw new \Exception('invalid status');
            }

            if (!is_numeric($row[18]) || (intval($row[18]) !== 0 && intval($row[18]) !== 1)) {
                throw new \Exception('invalid top');
            }

            $mediaAttributes = [
                'is_done' => is_null($row[17]) ? 0 : $row[17],
                'is_top' => is_null($row[18]) ? 0 : $row[18],
                'creator' => $row[1],
                'updated_at' => Carbon::now()
            ];

            $metaAttributes = [
                'event' => $row[2],
                'game' => $row[3],
                'game_place' => $row[4],
                'game_date' => $row[5],
                'game_time' => $row[6],
                'home_team' => $row[7],
                'away_team' => $row[8],
                'players' => $row[9],
                'subject1' => $row[10],
                'subject2' => $row[11],
                'subject3' => $row[12],
                'state1' => $row[13],
                'state2' => $row[14],
                'state3' => $row[15],
                'group_name' => $row[16],
            ];

            $media->update($mediaAttributes);
            $media->meta()->updateOrCreate([
                'media_id' => $media->id
            ], $metaAttributes);
        } else {
            foreach ($row as $item) {
                $valid = false;
                
                foreach ($this->keys as $key) {
                    if ($item === $key) {
                        $valid = true;
                    }
                }

                if ($valid === false) {
                    throw new \Exception('invalid file');
                }
            }
        }
    }
}
