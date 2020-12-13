<?php

namespace App;

use DateTime;
use SimpleXMLElement;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Kakolog extends Model
{
    /**
     * 過去ログを取得する
     * 指定された開始時刻/終了時刻の xml ファイルがあれば生の過去ログデータを返す
     * 指定された時間範囲の過去ログが存在しない場合はエラーメッセージの入った配列を返す
     *
     * @param string $jikkyo_id 実況ID (ex: jk211)
     * @param integer $starttime 取得を開始する時刻のタイムスタンプ
     * @param integer $endtime 取得を終了する時刻のタイムスタンプ
     * @return string|array 生の過去ログデータ or エラーメッセージの入った配列
     */
    public static function getKakolog(string $jikkyo_id, int $starttime, int $endtime)
    {

        // DateTime オブジェクトにする
        $start_datetime = new DateTime("@{$starttime}");
        $end_datetime = new DateTime("@{$endtime}");

        // 取得開始時刻が取得終了時刻より小さいか
        if ($starttime < $endtime) {

            // 取得開始/終了時刻双方の .nicojk ファイルが存在するなら続行
            if (Storage::disk('local')->exists(Kakolog::getKakologFileName($jikkyo_id, $start_datetime)) and
                Storage::disk('local')->exists(Kakolog::getKakologFileName($jikkyo_id, $end_datetime))) {

                // 現在作業している日付
                $current_datetime = $start_datetime;

                // 過去ログ、この文字列に足していく
                $kakolog = '';

                // 終了時刻の日付になるまで日付を足し続ける
                for (; $current_datetime->getTimeStamp() <= $endtime; $current_datetime->modify('+1 days')) {

                    // 過去ログを取得（ trim() で両端の改行を除去しておく）
                    $kakolog_file = trim(Storage::disk('local')->get(Kakolog::getKakologFileName($jikkyo_id, $current_datetime)));

                    // 開始/終了時刻の日付のみ
                    if ($start_datetime->format('Ymd') === $current_datetime->format('Ymd') or
                        $end_datetime->format('Ymd') === $current_datetime->format('Ymd')) {

                        // コメントを <chat> 要素ごとに分割する（ \n で分割しないのはまれに複数行コメントが存在するため）
                        $kakolog_array = explode('</chat>', $kakolog_file);

                        // start_datetime よりも前のコメントを削除
                        foreach ($kakolog_array as $key => $value) {
                            
                            // コメントのタイムスタンプを抽出
                            preg_match('/date="([0-9]+?)"/s', $value, $matches);

                            // タイムスタンプが存在しない（空要素など）
                            if (!isset($matches[1])) {
                                // 当該要素を削除して次のループへ
                                unset($kakolog_array[$key]);
                                continue;
                            }
                            $timestamp = $matches[1];

                            // 開始時刻の日付のみ、開始時刻のタイムスタンプよりも小さいコメントを削除
                            if ($start_datetime->format('Ymd') === $current_datetime->format('Ymd')) {
                                if ($timestamp < $starttime) {
                                    unset($kakolog_array[$key]);
                                }
                            }

                            // 終了時刻の日付のみ、終了時刻のタイムスタンプよりも大きいコメントを削除
                            if ($end_datetime->format('Ymd') === $current_datetime->format('Ymd')) {
                                if ($timestamp > $endtime) {
                                    unset($kakolog_array[$key]);
                                }
                            }
                        }

                        // 分割したコメントを結合して $kakolog に追記
                        // 前後の改行は trim() で削除しておく
                        $kakolog = trim($kakolog.implode('</chat>', $kakolog_array).'</chat>');

                    // それ以外の日付
                    } else {

                        // そのまま追記
                        $kakolog = $kakolog . $kakolog_file;
                    }
                }

                // 生の過去ログデータを返す
                return $kakolog;

            // 存在しないのでエラーを返す
            } else {
                return [
                    'error' => 'The kakolog in the specified time range does not exist.',
                ];
            }
        
        // 取得開始時刻が取得終了時刻より大きいのでエラー
        } else {
            return [
                'error' => 'The specified start time is after the end time.',
            ];
        }
    }


    /**
     * 過去ログのファイル名を取得する
     *
     * @param string $jikkyo_id 実況ID
     * @param DateTime $_datetime DateTime オブジェクト
     * @return string 過去ログのファイル名
     */
    private static function getKakologFileName(string $jikkyo_id, DateTime $_datetime): string
    {
        return "kakolog/{$jikkyo_id}/{$_datetime->format('Y')}/{$_datetime->format('Ymd')}.nicojk";
    }
    

    /**
     * 生の過去ログデータを XML にフォーマットする
     *
     * @param string $kakolog_raw 生の過去ログデータ
     * @return string XML にフォーマットした過去ログデータ
     */
    public static function formatToXML(string $kakolog_raw): string
    {

        // XML のヘッダをつけてるだけなので Valid な XML かは微妙（たまに壊れてるのとかあるし）
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<packet>\n{$kakolog_raw}\n</packet>";
    }
    

    /**
     * 生の過去ログデータを JSON にフォーマットする
     *
     * @param string $kakolog_raw 生の過去ログデータ
     * @return string JSON にフォーマットした過去ログデータ
     */
    public static function formatToJSON(string $kakolog_raw): string
    {
        
        // まずは XML に直す
        $kakolog_xml = Kakolog::formatToXML($kakolog_raw);

        // JSON に変換した XML オブジェクト？として読み込む
        // 参考: https://stackoverflow.com/a/31273676
        $kakolog_object = new JsonSerializer($kakolog_xml);

        // json_encode() で JSON にフォーマット
        return json_encode($kakolog_object, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); 
    }
}
