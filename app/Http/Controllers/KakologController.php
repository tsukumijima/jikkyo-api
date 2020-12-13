<?php

namespace App\Http\Controllers;

use App\Kakolog;
use Illuminate\Http\Request;

class KakologController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $jikkyo_id, Request $request)
    {
        // クエリパラメータがあるか確認
        if ($request->has(['format', 'starttime', 'endtime'])) {

            // フォーマット
            $format = strtolower($request->input('format'));

            // 取得開始/終了時刻のタイムスタンプ
            $starttime = $request->input('starttime');
            $endtime = $request->input('endtime');

            // XML でも JSON でもない場合はエラー
            if ($format !== 'xml' and $format !== 'json') {
                $message = Kakolog::errorMessage(['フォーマットは XML または JSON 形式である必要があります。'], 'json');  // JSON 決め打ち
                return response($message)->header('Content-Type', 'application/json');
            }

            // 有効なタイムスタンプでない場合はエラー
            if (!$this->isValidTimeStamp($starttime) or !$this->isValidTimeStamp($endtime)) {
                $message = Kakolog::errorMessage(['開始時刻または終了時刻が不正です。'], $format);
                return response($message)->header('Content-Type', "application/{$format}");
            }

            // 生の過去ログを取得
            // xml ヘッダはついていない
            $kakolog_raw = Kakolog::getKakolog($jikkyo_id, intval($starttime), intval($endtime));

            // 指定された時間範囲の過去ログが存在しない
            if (is_array($kakolog_raw)) {
                $message = Kakolog::errorMessage($kakolog_raw, $format);
                return response($message)->header('Content-Type', "application/{$format}");
            }

            // XML にフォーマットして返す
            if ($format === 'xml') {

                $kakolog_xml = Kakolog::formatToXml($kakolog_raw);

                return response($kakolog_xml)->header('Content-Type', 'application/xml');

            // JSON にフォーマットして返す
            } else if ($format === 'json') {

                $kakolog_json = Kakolog::formatToJson($kakolog_raw);

                return response($kakolog_json)->header('Content-Type', 'application/json');
            }

        } else {

            // 必要なパラメータが存在しない
            $message = Kakolog::errorMessage(['必要なパラメーターが存在しません。'], 'json');  // JSON 決め打ち
            return response($message)->header('Content-Type', 'application/json');
        }
    }


    /**
     * 有効なタイムスタンプかどうかを返す 
     *
     * @param mixed $timestamp タイムスタンプ
     * @return boolean 有効なタイムスタンプかどうか
     */
    private function isValidTimeStamp($timestamp) {
        // 0 以上で現在のタイムスタンプ以下の数値
        return is_numeric($timestamp) and intval($timestamp) >= 0 and intval($timestamp) <= time();
    }
}
