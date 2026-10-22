# jikkyo-api

KakologArchives の過去ログを、ニコニコ実況のコメントとして JSON または XML で取得できる API です。  
現在の実行環境は PHP 8.5 と Laravel 13 です。

API の利用方法は [ニコニコ実況 過去ログ API](https://jikkyo.tsukumijima.net/)をご覧ください。

## 開発環境

開発とテストには Docker Compose を使います。  
PHP や Composer をホストへ追加する必要はなく、Mac と Linux のどちらでも同じコマンドで実行できます。

```shell
docker compose up --build
```

起動後は `http://localhost:5200/` で API の案内ページを確認できます。  
初回起動時には名前付きボリュームへ Composer の依存関係をインストールするため、2回目以降より時間がかかります。

### テストとコード検査

```shell
docker compose run --rm app composer test
docker compose run --rm app composer lint
```

整形が必要な場合は次のコマンドを使います。

```shell
docker compose run --rm app composer format
```

## License

[MIT License](License.txt)
