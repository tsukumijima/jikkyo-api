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
            if ($kakolog_raw === false) {
                return [
                    'error' => 'The kakolog in the specified time range does not exist.',
                ];
            }

            // XML または JSON にフォーマット
            if ($format === 'xml') {
                $kakolog = Kakolog::formatToXML($kakolog_raw);
            } else if ($format === 'json') {
                $kakolog = Kakolog::formatToJson($kakolog_raw);
            } else {
                // XML でも JSON でもなかったらエラー
                return [
                    'error' => 'The format must be xml or json.',
                ];
            }

            // 過去ログを返す
            return $kakolog;

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
