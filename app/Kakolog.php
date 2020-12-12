<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kakolog extends Model
{
    /**
     * 過去ログを取得する
     * 指定された開始時刻/終了時刻の xml ファイルがあれば生の過去ログデータを、なければ false を返す
     *
     * @param string $jikkyo_id 実況ID (ex: jk211)
     * @param integer $starttime 取得を開始する時刻のタイムスタンプ
     * @param integer $endtime 取得を終了する時刻のタイムスタンプ
     * @return mixed 生の過去ログデータか false
     */
    public static function getKakolog(string $jikkyo_id, int $starttime, int $endtime)
    {
        //
    }
    

    /**
     * 生の過去ログデータを XML にフォーマットする
     *
     * @param string $kakolog_raw 生の過去ログデータ
     * @return string XML にフォーマットした過去ログデータ
     */
    public static function formatToXML(string $kakolog_raw) {
        //
    }
    

    /**
     * 生の過去ログデータを JSON にフォーマットする
     *
     * @param string $kakolog_raw 生の過去ログデータ
     * @return string JSON にフォーマットした過去ログデータ
     */
    public static function formatToJSON(string $kakolog_raw) {
        //
    }
}
