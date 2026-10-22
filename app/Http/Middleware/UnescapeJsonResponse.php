<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class UnescapeJsonResponse
{
    /**
     * JSON レスポンスの日本語とスラッシュをそのまま出力する。
     * 参考: https://nextat.co.jp/staff/archives/203
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // JSON 以外はそのまま返す
        if (! $response instanceof JsonResponse) {
            return $response;
        }

        // エンコードオプションを追加して設定し直す
        // JSON_PRETTY_PRINT はローカル環境のみ
        if (\App::isLocal()) {
            $newEncodingOptions = $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        } else {
            $newEncodingOptions = $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        }
        $response->setEncodingOptions($newEncodingOptions);

        return $response;
    }
}
