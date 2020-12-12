<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\JsonResponse;

class UnescapeJsonResponse
{
    /**
     * Handle an incoming request.
     * 参考: https://nextat.co.jp/staff/archives/203
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // JSON以外はそのまま
        if (!$response instanceof JsonResponse) {
            return $response;
        }

        // エンコードオプションを追加して設定し直す
        $newEncodingOptions = $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        $response->setEncodingOptions($newEncodingOptions);

        return $response;
    }
}
