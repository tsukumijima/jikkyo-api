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

            // 有効なタイムスタンプでない場合はエラー
            if (!$this->isValidTimeStamp($starttime) or !$this->isValidTimeStamp($endtime)) {
                return [
                    'error' => 'The start time or end time is invalid.',
                ];
            }

            // 生の過去ログを取得
            // xml ヘッダはついていない
            $kakolog_raw = Kakolog::getKakolog($jikkyo_id, intval($starttime), intval($endtime));

            // 指定された時間範囲の過去ログが存在しない
            if (is_array($kakolog_raw)) {
                return $kakolog_raw;
            }

            // XML にフォーマット
            if ($format === 'xml') {

                return response(Kakolog::formatToXML($kakolog_raw))->header('Content-Type', 'application/xml');

            // JSON にフォーマット
            } else if ($format === 'json') {

                return response(Kakolog::formatToJson($kakolog_raw))->header('Content-Type', 'application/json');
    
            // XML でも JSON でもなかったらエラー
            } else {
                return [
                    'error' => 'The format must be xml or json.',
                ];
            }

        } else {

            // エラーを返す (JSON)
            return [
                'error' => 'The required parameter does not exist.',
            ];
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
