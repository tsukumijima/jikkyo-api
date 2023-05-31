<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Kakolog extends Model
{
    /**
     * 過去ログを取得する
     * 指定された開始時刻/終了時刻の xml ファイルがあれば生の過去ログデータを返す
     * 指定された期間の過去ログが存在しないなど、取得できなかった場合はエラーメッセージを返す
     *
     * @param string $jikkyo_id 実況ID (ex: jk211)
     * @param integer $start_time 取得を開始する時刻のタイムスタンプ
     * @param integer $end_time 取得を終了する時刻のタイムスタンプ
     * @return array [生の過去ログデータ or エラーメッセージ, 取得できたかどうか]
     */
    public static function getKakolog(string $jikkyo_id, int $start_time, int $end_time): array
    {
        // DateTime オブジェクトにする
        // setTimestamp() を使わないとロケールが考慮されないらしい…？
        $start_date = new DateTime();
        $start_date->setTimestamp($start_time);
        $end_date = new DateTime();
        $end_date->setTimestamp($end_time);

        // 有効なタイムスタンプでない場合はエラー
        if (!Kakolog::isValidTimeStamp($start_time) or !Kakolog::isValidTimeStamp($end_time)) {
            return ['取得開始時刻または取得終了時刻が不正です。', false];
        }

        // 取得開始時刻と取得終了時刻が同じ
        if ($start_time === $end_time) {
            return ['取得開始時刻と取得終了時刻が同じ時刻です。', false];
        }

        // 取得開始時刻が取得終了時刻より大きい
        if ($start_time >= $end_time) {
            return ['指定された取得開始時刻は取得終了時刻よりも後です。', false];
        }

        // 取得開始時刻～取得終了時刻が3日間を超えている
        // 3日間ぴったりだけ許可する
        if (($start_date->diff($end_date)->days >= 3) and
            !($start_date->diff($end_date)->days === 3 and $start_date->diff($end_date)->h === 0 and
              $start_date->diff($end_date)->i === 0 and $start_date->diff($end_date)->s === 0)) {
            return ['3日分を超えるコメントを一度に取得することはできません。数日分かに分けて取得するようにしてください。', false];
        }

        // $start_date, $end_date は日付の比較用なので、時間:分:秒 の情報は削除して 00:00:00 に統一
        $start_date->setTime(0, 0, 0);
        $end_date->setTime(0, 0, 0);

        // 現在作業している日付
        $current_date = $start_date;

        // 過去ログ、この文字列に足していく
        $kakolog = '';

        // 終了時刻の日付になるまで日付を足し続ける
        for (; $current_date->getTimeStamp() <= $end_time; $current_date->modify('+1 days')) {

            // Hugging Face から過去ログを取得
            // 3回までリトライする
            $kakolog_file_name = Kakolog::getKakologFilePath($jikkyo_id, $current_date);
            $kakolog_file_url = "https://huggingface.co/datasets/KakologArchives/KakologArchives/resolve/main/{$kakolog_file_name}";
            $retry_count = 3;
            while ($retry_count > 0) {
                try {
                    $kakolog_response = HTTP::withOptions(['verify' => false])->get($kakolog_file_url);
                    break;
                } catch (Illuminate\Http\Client\ConnectionException $e) {
                    $retry_count--;
                    if ($retry_count === 0) throw $e;  // 3回失敗したら例外を投げる
                    sleep(1);  // 1秒待機
                }
            }

            // その日付の過去ログファイル (.nicojk) が存在しない
            // Hugging Face 上でステータスコードが 404 であれば存在しないものとする
            if ($kakolog_response->status() === 404) {

                // 指定された実況チャンネルが（過去を含め）存在しない場合はここでエラーにする
                // 過去ログが存在するならこの判定は不要なので、レスポンス高速化のためにその日付の過去ログが存在しない場合のみ判定を行う
                if (Http::get("https://huggingface.co/datasets/KakologArchives/KakologArchives/tree/main/{$jikkyo_id}")->status() === 404) {
                    return ['指定された実況 ID は存在しません。', false];
                }

                // ファイルだけが存在しない場合は以降の処理をスキップ
                continue;

            // 404 ではないが、200 (成功) でもない場合
            // Hugging Face の障害が考えられるので、その旨を表示する
            } else if ($kakolog_response->status() !== 200) {
                return ["Hugging Face で障害が発生しているため、過去ログを取得できません。(HTTP Error {$kakolog_response->status()})", false];
            }

            // 過去ログを取得（ trim() で両端の改行を除去しておく）
            $kakolog_file = trim($kakolog_response->body());

            // 開始/終了時刻の日付のみ
            if ($start_date->getTimeStamp() === $current_date->getTimeStamp() or
                $end_date->getTimeStamp() === $current_date->getTimeStamp()) {

                // コメントを <chat> 要素ごとに分割する（ \n で分割しないのはまれに複数行コメントが存在するため）
                $kakolog_array = explode('</chat>', $kakolog_file);

                // start_date よりも前のコメントを削除
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
                    if ($start_date->getTimeStamp() === $current_date->getTimeStamp()) {
                        if ($timestamp < $start_time) {
                            unset($kakolog_array[$key]);
                        }
                    }

                    // 終了時刻の日付のみ、終了時刻のタイムスタンプよりも大きいコメントを削除
                    if ($end_date->getTimeStamp() === $current_date->getTimeStamp()) {
                        if ($timestamp > $end_time) {
                            unset($kakolog_array[$key]);
                        }
                    }
                }

                // 一度配列に分割したコメントを implode() で文字列に戻す
                $kakolog_implode = implode('</chat>', $kakolog_array).'</chat>';

                // もし末尾が "/></chat>" になってしまった場合は、"/>" に置換する
                $kakolog_implode = str_replace('/></chat>', '/>', $kakolog_implode);

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

            // 改行を追加
            $kakolog = $kakolog . "\n";
        }

        // 最初と最後にあるかもしれない改行を削除
        $kakolog = trim($kakolog);

        // 生の過去ログデータを返す
        return [$kakolog, true];
    }


    /**
     * 過去ログのファイルパスを取得する
     *
     * @param string $jikkyo_id 実況ID
     * @param DateTime $datetime DateTime オブジェクト
     * @return string 過去ログのファイルパス (例: jk1/2021/20210101.nicojk)
     */
    private static function getKakologFilePath(string $jikkyo_id, DateTime $datetime): string
    {
        return "{$jikkyo_id}/{$datetime->format('Y')}/{$datetime->format('Ymd')}.nicojk";
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
