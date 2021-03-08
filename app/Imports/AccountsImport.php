<?php

namespace App\Imports;

use App\Models\Profile;
use App\Models\User;
use App\Models\Admin;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;


class AccountsImport implements ToModel, WithBatchInserts, WithChunkReading
{
//    private $keys = [
//        'クラブ名',
//        '選手名'
//    ];
    private $keys = [
        '選手番号',
        'チーム名漢字',
        'チーム名英字',
        '選手氏名漢字',
        '選手氏名英字'
    ];
    private $chunk_size=500;
    public function batchSize(): int
    {
        return $this->chunk_size;
    }
    
    public function chunkSize(): int
    {
        return $this->chunk_size;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
     * @throws
    */
    public function model(array $row)
    {
        if (sizeof($row) !== 5) {
            //logger()->error("row size=".sizeof($row));
            throw new \Exception('invalid file');
        }

        /**
         * 0: 選手番号
         * 1: チーム名漢字
         * 2: チーム名英字
         * 3: 選手氏名漢字
         * 4: 選手氏名英字
         */
        if($row[0] === $this->keys[0]) {
            $invalidHeader = $row[0] !== $this->keys[0] ||
                $row[1] !== $this->keys[1] ||
                $row[2] !== $this->keys[2] ||
                $row[3] !== $this->keys[3] ||
                $row[4] !== $this->keys[4];

          if($invalidHeader) {
              //logger()->error('header='.implode('|', $row));
              throw new \Exception('invalid file');
          }
        } else {
            $invalidValue = $row[0] === '' || $row[1] === '' || $row[2] === '' || $row[3] === '' || $row[4] === '';

            if($invalidValue) {
                //logger()->error('row='.implode('|', $row));
                throw new \Exception('invalid file');
            }

            \DB::beginTransaction();
            try {
                $club = Admin::where('name', $row[1])->first();

                $hasClubInfo = !(empty($row[1]) || empty($row[2]));

                if(is_null($club) && $hasClubInfo) {
                    $club = Admin::create([
                        'email' => str_replace(' ', '_', $row[2]).'@mail.com',
                        'password' => bcrypt('club1234'),
                        'name_short' => '****',
                        'role' => 'club',
                        'name' => $row[1] ?? '',
                        'name_en' => $row[2] ?? ''
                    ]);
                }

                $user = User::where('player_no', $row[0])->first();
                if(is_null($user)) {
                    $user = User::create([
                        'email' => 'dump' . time() . '@mail.com',
                        'password' => bcrypt('12345678'),
                        'name' => $row[3] ?? '',
                        'name_en' => $row[4] ?? '',
                        'player_no' => $row[0],
                        'status' => 0,
                    ]);

                    $profile = Profile::create([
                        'user_id' => $user->id,
                        'admin_id' => is_null($club) ? null : $club->id
                    ]);
                } else {
                    $user->name = $row[3];
                    $user->name_en = $row[4];
                    $user->player_no = $row[0];
                    $user->save();

                    $profile = $user->profile;
                    $profile->admin_id = is_null($club) ? null : $club->id;
                    $profile->save();
                }
            } catch (\Exception $e) {
                //logger()->error($e->getMessage());
                \DB::rollBack();
                throw new \Exception('save error');
            }
            \DB::commit();
        }
    }
    /*
    public function model(array $row)
    {
        if (sizeof($row) !== 2) {
            throw new \Exception('invalid file');
        }

        if ($row[0] !== 'クラブ名') {
            $user = null;

            if (isset($row[1])) { // Update
                $user = User::where('name', $row[1])->first();
            }

            $userAttributes = [
                'name' => $row[1],
                'status' => 0
            ];

            if (!isset($user)) {
                $user = User::create($userAttributes);
            }

            $club = null;

            if (isset($row[0])) {
                $club = Admin::where('name', $row[0])->first();
            }

            if (!isset($club)) {
                return;
            }

            $profileAttributes = [
                'admin_id' => $club->id
            ];

            $user->profile()->updateOrCreate([
                'user_id' => $user->id
            ], $profileAttributes);
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
    */
}
