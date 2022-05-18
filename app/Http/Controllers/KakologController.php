<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kakolog;

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
            $start_time = $request->input('starttime');
            $end_time = $request->input('endtime');

            // XML でも JSON でもない場合はエラー
            if ($format !== 'xml' and $format !== 'json') {
                $message = Kakolog::errorMessage('フォーマットは XML または JSON 形式である必要があります。', 'json');  // JSON 決め打ち
                return response($message)->header('Content-Type', 'application/json');
            }

            // 生の過去ログを取得
            // xml ヘッダはついていない
            list($kakolog_raw, $kakolog_result) = Kakolog::getKakolog($jikkyo_id, intval($start_time), intval($end_time));

            // 指定された期間の過去ログが存在しない
            if ($kakolog_result === false) {
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
            $message = Kakolog::errorMessage('必要なパラメータが存在しません。', 'json');  // JSON 決め打ち
            return response($message)->header('Content-Type', 'application/json');
        }
    }
}
