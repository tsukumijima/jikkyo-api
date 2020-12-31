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
     * 指定された期間の過去ログが存在しないなど取得できなかった場合はエラーメッセージを返す
     *
     * @param string $jikkyo_id 実況ID (ex: jk211)
     * @param integer $starttime 取得を開始する時刻のタイムスタンプ
     * @param integer $endtime 取得を終了する時刻のタイムスタンプ
     * @return array [生の過去ログデータ or エラーメッセージ, 取得できたかどうか]
     */
    public static function getKakolog(string $jikkyo_id, int $starttime, int $endtime): array
    {

        // DateTime オブジェクトにする
        // setTimestamp() を使わないとロケールが考慮されないらしい…？
        $startdate = new DateTime();
        $startdate->setTimestamp($starttime);
        $enddate = new DateTime();
        $enddate->setTimestamp($endtime);

        // $startdate, $enddate は日付の比較用なので、時間:分:秒 の情報は削除して 00:00:00 に統一
        $startdate->setTime(0, 0, 0);
        $enddate->setTime(0, 0, 0);

        // 指定された実況チャンネルが（過去を含め）存在しない
        if (!Storage::disk('local')->exists("kakolog/{$jikkyo_id}")) {
            return ['指定された実況 ID は存在しません。', false];
        }

        // 有効なタイムスタンプでない場合はエラー
        if (!Kakolog::isValidTimeStamp($starttime) or !Kakolog::isValidTimeStamp($endtime)) {
            return ['取得開始時刻または取得終了時刻が不正です。', false];
        }

        // 取得開始時刻と取得終了時刻が同じ
        if ($starttime === $endtime) {
            return ['取得開始時刻と取得終了時刻が同じ時刻です。', false];
        }

        // 取得開始時刻が取得終了時刻より大きい
        if ($starttime >= $endtime) {
            return ['指定された取得開始時刻は取得終了時刻よりも後です。', false];
        }

        // 取得開始/終了時刻どちらかの .nicojk ファイルが存在しない
        if (!Storage::disk('local')->exists(Kakolog::getKakologFileName($jikkyo_id, $startdate)) or
            !Storage::disk('local')->exists(Kakolog::getKakologFileName($jikkyo_id, $enddate))) {
            return ['指定された期間の過去ログは存在しません。', false];
        }

        // 取得開始時刻～取得終了時刻が3日間を超えている
        if (intval($startdate->diff($enddate)->format('%R%a')) > 3) {
            return ['3日分を超えるコメントを一度に取得することはできません。数日分かに分けて取得するようにしてください。', false];
        }

        // 現在作業している日付
        $currentdate = $startdate;

        // 過去ログ、この文字列に足していく
        $kakolog = '';

        // 終了時刻の日付になるまで日付を足し続ける
        for (; $currentdate->getTimeStamp() <= $endtime; $currentdate->modify('+1 days')) {

            // 過去ログを取得（ trim() で両端の改行を除去しておく）
            $kakolog_file = trim(Storage::disk('local')->get(Kakolog::getKakologFileName($jikkyo_id, $currentdate)));

            // 開始/終了時刻の日付のみ
            if ($startdate->getTimeStamp() === $currentdate->getTimeStamp() or
                $enddate->getTimeStamp() === $currentdate->getTimeStamp()) {

                // コメントを <chat> 要素ごとに分割する（ \n で分割しないのはまれに複数行コメントが存在するため）
                $kakolog_array = explode('</chat>', $kakolog_file);

                // startdate よりも前のコメントを削除
                foreach ($kakolog_array as $key => $value) {
                    
                    // コメントのタイムスタンプを正規表現で抽出
                    preg_match('/date="([0-9]+?)"/s', $value, $matches);

                    // タイムスタンプが存在しない（</chat>で分割した最後の要素、空要素など）
                    if (!isset($matches[1])) {
                        // 当該要素を削除して次のループへ
                        unset($kakolog_array[$key]);
                        continue;
                    }
                    // コメントのタイムスタンプ
                    $timestamp = $matches[1];

                    // 開始時刻の日付のみ、開始時刻のタイムスタンプよりも小さいコメントを削除
                    if ($startdate->getTimeStamp() === $currentdate->getTimeStamp()) {
                        if ($timestamp < $starttime) {
                            unset($kakolog_array[$key]);
                        }
                    }

                    // 終了時刻の日付のみ、終了時刻のタイムスタンプよりも大きいコメントを削除
                    if ($enddate->getTimeStamp() === $currentdate->getTimeStamp()) {
                        if ($timestamp > $endtime) {
                            unset($kakolog_array[$key]);
                        }
                    }
                }

                // 一度配列に分割したコメントを implode() で文字列に戻す
                $kakolog_implode = implode('</chat>', $kakolog_array).'</chat>';

                // 内容が </chat> しかない（＝指定期間のコメントが存在しない）場合は空に設定
                if ($kakolog_implode === '</chat>') {
                    $kakolog_implode = '';
                }

                // $kakolog に追記
                // 前後の改行は trim() で削除しておく
                $kakolog = $kakolog . trim($kakolog_implode);

            // それ以外の日付
            } else {

                // そのまま追記
                $kakolog = $kakolog . $kakolog_file;
            }
        }

        // 生の過去ログデータを返す
        return [$kakolog, true];
    }


    /**
     * 過去ログのファイル名を取得する
     *
     * @param string $jikkyo_id 実況ID
     * @param DateTime $datetime DateTime オブジェクト
     * @return string 過去ログのファイル名
     */
    private static function getKakologFileName(string $jikkyo_id, DateTime $datetime): string
    {
        return "kakolog/{$jikkyo_id}/{$datetime->format('Y')}/{$datetime->format('Ymd')}.nicojk";
    }


    /**
     * 有効なタイムスタンプかどうかを返す 
     *
     * @param mixed $timestamp タイムスタンプ
     * @return boolean 有効なタイムスタンプかどうか
     */
    private static function isValidTimeStamp($timestamp) {
        // 0 以上で現在のタイムスタンプ以下の数値
        return is_numeric($timestamp) and intval($timestamp) >= 0 and intval($timestamp) <= time();
    }


    /**
     * エラーメッセージを指定の形式でフォーマットして返す
     * 複数の要素がある場合は改行で連結する
     *
     * @param string $message エラーメッセージ
     * @param string $format フォーマット形式 (XML or JSON)
     * @return string フォーマットしたエラーメッセージ
     */
    public static function errorMessage(string $message, string $format): string
    {

        // XML の場合
        if ($format === 'xml') {
            
            return Kakolog::formatToXml("<error>{$message}</error>");

        // JSON の場合
        } else if ($format === 'json') {

            $message_array = ['error' => $message];

            // json_encode() で JSON にフォーマット
            // JSON_PRETTY_PRINT はローカル環境のみ（コメントデータが大量だとスペースや改行の分データが余計に増えて重くなる）
            if (\App::isLocal()) {
                return json_encode($message_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); 
            } else {
                return json_encode($message_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
            }
        }
    }
    

    /**
     * 生の過去ログデータを XML にフォーマットする
     *
     * @param string $kakolog_raw 生の過去ログデータ
     * @return string XML にフォーマットした過去ログデータ
     */
    public static function formatToXml(string $kakolog_raw): string
    {

        // XML のヘッダをつけてるだけなので Valid な XML かは微妙（たまに壊れてるのとかあるし）
        if (strpos($kakolog_raw, '<error>') !== false) {  // <error> が存在するか
            return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n{$kakolog_raw}";
        // 取得したコメントが存在しない
        } else if (trim($kakolog_raw) === '') {
            return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<packet>\n</packet>";
        } else {
            return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<packet>\n{$kakolog_raw}\n</packet>";
        }
    }
    

    /**
     * 生の過去ログデータを JSON にフォーマットする
     *
     * @param string $kakolog_raw 生の過去ログデータ
     * @return string JSON にフォーマットした過去ログデータ
     */
    public static function formatToJson(string $kakolog_raw): string
    {
        
        // まずは XML に直す
        $kakolog_xml = Kakolog::formatToXml($kakolog_raw);

        // JSON に変換した XML オブジェクト？として読み込む
        // 参考: https://stackoverflow.com/a/31273676
        $kakolog_object = new JsonSerializer($kakolog_xml);

        // json_encode() で JSON にフォーマット
        // JSON_PRETTY_PRINT はローカル環境のみ（コメントデータが大量だとスペースや改行の分データが余計に増えて重くなる）
        if (\App::isLocal()) {
            return json_encode($kakolog_object, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); 
        } else {
            return json_encode($kakolog_object, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
        }
    }
}
