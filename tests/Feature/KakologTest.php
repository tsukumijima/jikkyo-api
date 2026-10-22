<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class KakologTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function json_preserves_attributes_unicode_and_multiline_comments(): void
    {
        // 時間範囲の両端を含め、範囲外のコメントだけを除外
        Http::fake(['huggingface.co/*' => Http::response(
            '<chat date="1609459199">範囲外</chat>'.
            '<chat date="1609459200" user_id="001" mail="">日本語 https://example.com/</chat>'.
            "<chat date=\"1609459260\">複数行\nコメント &amp; 記号</chat>".
            '<chat date="1609459261">範囲外</chat>',
        )]);

        $response = $this->get('/api/kakolog/jk1?format=JSON&starttime=1609459200&endtime=1609459260');
        $response->assertOk()->assertHeader('Content-Type', 'application/json')
            ->assertExactJson(['packet' => [
                ['chat' => ['date' => '1609459200', 'user_id' => '001', 'mail' => '', 'content' => '日本語 https://example.com/']],
                ['chat' => ['date' => '1609459260', 'content' => "複数行\nコメント & 記号"]],
            ]]);
        $this->assertStringContainsString('日本語 https://example.com/', $response->getContent());
        Http::assertSent(fn ($request) => $request->url() === 'https://huggingface.co/datasets/KakologArchives/KakologArchives/resolve/main/jk1/2021/20210101.nicojk');
    }

    #[Test]
    public function xml_preserves_the_original_comment_elements(): void
    {
        $comment = '<chat date="1609459200" user_id="001">日本語 &amp; 記号</chat>';
        Http::fake(['huggingface.co/*' => Http::response($comment)]);

        $this->get('/api/kakolog/jk1?format=xml&starttime=1609459200&endtime=1609459260')
            ->assertOk()->assertHeader('Content-Type', 'application/xml')
            ->assertContent("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<packet>\n{$comment}\n</packet>");
    }

    #[Test]
    public function a_period_crossing_midnight_reads_both_days_in_order(): void
    {
        Http::fake([
            '*/20210101.nicojk' => Http::response('<chat date="1609513199">前日</chat>'),
            '*/20210102.nicojk' => Http::response('<chat date="1609513200">翌日</chat>'),
        ]);

        $this->get('/api/kakolog/jk1?format=json&starttime=1609513199&endtime=1609513201')
            ->assertOk()->assertExactJson(['packet' => [
                ['chat' => ['date' => '1609513199', 'content' => '前日']],
                ['chat' => ['date' => '1609513200', 'content' => '翌日']],
            ]]);
        Http::assertSentCount(2);
    }

    #[Test]
    public function missing_day_returns_an_empty_packet_for_an_existing_channel(): void
    {
        Http::fake([
            '*/resolve/*' => Http::response('', 404),
            '*/tree/*' => Http::response('', 200),
        ]);

        $this->get('/api/kakolog/jk1?format=json&starttime=1609459200&endtime=1609459260')
            ->assertOk()->assertExactJson(['packet' => []]);
        $this->get('/api/kakolog/jk1?format=xml&starttime=1609459200&endtime=1609459260')
            ->assertOk()->assertContent("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<packet>\n</packet>");
    }

    #[Test]
    public function an_unknown_channel_returns_the_existing_error(): void
    {
        Http::fake(['huggingface.co/*' => Http::response('', 404)]);

        $this->get('/api/kakolog/unknown?format=json&starttime=1609459200&endtime=1609459260')
            ->assertOk()->assertExactJson(['error' => '指定された実況 ID は存在しません。']);
    }

    #[Test]
    public function upstream_failure_returns_an_xml_error_without_a_packet(): void
    {
        Http::fake(['huggingface.co/*' => Http::response('', 503)]);

        $this->get('/api/kakolog/jk1?format=xml&starttime=1609459200&endtime=1609459260')
            ->assertOk()->assertContent('<?xml version="1.0" encoding="UTF-8"?>'."\n".
                '<error>Hugging Face で障害が発生しているため、過去ログを取得できません。(HTTP Error 503)</error>');
    }

    #[Test]
    #[DataProvider('invalidQueries')]
    public function invalid_parameters_keep_the_error_body_and_http_200(string $query, string $message): void
    {
        $this->get('/api/kakolog/jk1'.$query)
            ->assertOk()->assertHeader('Content-Type', 'application/json')
            ->assertExactJson(['error' => $message]);
        Http::assertNothingSent();
    }

    public static function invalidQueries(): array
    {
        return [
            'missing' => ['', '必要なパラメータが存在しません。'],
            'format' => ['?format=csv&starttime=1609459200&endtime=1609459260', 'フォーマットは XML または JSON 形式である必要があります。'],
            'equal' => ['?format=json&starttime=1609459200&endtime=1609459200', '取得開始時刻と取得終了時刻が同じ時刻です。'],
            'reversed' => ['?format=json&starttime=1609459260&endtime=1609459200', '指定された取得開始時刻は取得終了時刻よりも後です。'],
            'negative' => ['?format=json&starttime=-1&endtime=1609459200', '取得開始時刻または取得終了時刻が不正です。'],
            'future' => ['?format=json&starttime=4102444800&endtime=4102444860', '取得開始時刻または取得終了時刻が不正です。'],
            'over_three_days' => ['?format=json&starttime=1609459200&endtime=1609718401', '3日分を超えるコメントを一度に取得することはできません。数日分かに分けて取得するようにしてください。'],
        ];
    }

    #[Test]
    public function exactly_three_days_remains_allowed(): void
    {
        Http::fake(['huggingface.co/*' => Http::response('')]);

        $this->get('/api/kakolog/jk1?format=json&starttime=1609459200&endtime=1609718400')
            ->assertOk()->assertExactJson(['packet' => []]);
        Http::assertSentCount(4);
    }

    #[Test]
    public function cors_and_more_than_sixty_requests_remain_available(): void
    {
        // 実際の API ルートで CORS と連続アクセスを確認
        for ($index = 0; $index < 65; $index++) {
            $this->withHeader('Origin', 'https://example.com')
                ->get('/api/kakolog/jk1')
                ->assertOk()->assertHeader('Access-Control-Allow-Origin', '*');
        }
        Http::assertNothingSent();
    }

    #[Test]
    public function home_page_renders_without_database_or_session(): void
    {
        $response = $this->get('/');
        $response->assertOk()->assertSee('ニコニコ実況 過去ログ API');
        $this->assertCount(0, $response->headers->getCookies());
    }
}
