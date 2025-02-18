<!DOCTYPE html>
<html lang="ja">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ニコニコ実況 過去ログ API</title>
  <link rel="icon" type="image/png" href="/logo.png">
  <link rel="canonical" href="{{ config('app.url') }}/">

  <!-- OGP -->
  <meta property="og:type" content="article">
  <meta property="description" content="ニコニコ実況の過去ログを XML や JSON データで返す非公式 API です。">
  <meta property="og:description" content="ニコニコ実況の過去ログを XML や JSON データで返す非公式 API です。">
  <meta property="og:title" content="ニコニコ実況 過去ログ API">
  <meta property="og:image" content="{{ config('app.url') }}/logo.png">
  <meta property="og:locale" content="ja_JP">
  <!-- /OGP -->

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary">
  <meta name="twitter:description" content="ニコニコ実況の過去ログを XML や JSON データで返す非公式 API です。">
  <meta name="twitter:title" content="ニコニコ実況 過去ログ API">
  <meta name="twitter:image" content="{{ config('app.url') }}/logo.png">
  <!-- /Twitter Card -->

  <!-- JavaScript -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment-with-locales.min.js"></script>
  <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
  <script type="text/javascript" src="/script.js"></script>

  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap">
  <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.13.1/css/all.css">
  <link rel="stylesheet" type="text/css" href="/style.css">

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('app.gtag') }}"></script>
  <script>

    window.dataLayer = window.dataLayer || [];
    function gtag(){
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', '{{ config('app.gtag') }}');

    $(function(){
      $('a[href^="#"]').click(function(){
        var adjust = -82; // ヘッダー分
        var speed = 450;
        var href= $(this).attr('href');
        var target = $(href == '#' || href == '' ? 'html' : href);
        var position = target.offset().top + adjust;
        $('body, html').animate({ scrollTop:position }, speed, 'swing');
        return false;
      });
    });

  </script>

</head>
<body>

  <nav id="navigation" class="navbar navbar-expand-md navbar-dark bg-info fixed-top">
    <div class="container">
      <a class="navbar-brand text-white" href="#">
        <img src="/logo.png" class="d-inline-block align-top">
        ニコニコ実況 過去ログ API
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link text-light" href="#about">
              <i class="fas fa-info-circle"></i>About
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="#notes">
              <i class="fas fa-exclamation-circle"></i>Notes
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="#request-parameter">
              <i class="fas fa-paper-plane"></i>Request
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="#response-field">
              <i class="fas fa-reply"></i>Response
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="#xml-data-sample">
              <i class="fas fa-code"></i>XML Data Sample
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="#json-data-sample">
              <i class="fas fa-code"></i>JSON Data Sample
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="alert alert-warning mb-0" role="alert">
    <div class="container px-0 px-sm-2-3">
      <div class="mb-0">
        <strong>「ニコニコ実況の Web 版非公式コメントビューア」＋「公式にない実況チャンネルを補完するコメントサーバー」、<a href="https://nx-jikkyo.tsukumijima.net" target="_blank">NX-Jikkyo</a> を運営中です！！<br>
        NX-Jikkyo から本家ニコニコ実況へコメントする機能を使えば、PC でもスマホでも使える軽量コメントビューアとして利用できます！<br>
        既に <a class="link" href="https://air.fem.jp/jkcommentviewer/" target="_blank">jkcommentviewer</a> / <a class="link" href="https://github.com/xtne6f/NicoJK/releases" target="_blank">TVTest (NicoJK)</a> / <a class="link" href="https://github.com/tsukumijima/KonomiTV/releases" target="_blank">KonomiTV</a> など多くの実況関連ソフトの最新版にて対応していただいています！<br>
        <a href="https://nx-jikkyo.tsukumijima.net" target="_blank">NX-Jikkyo</a> に投稿されたコメントは随時この過去ログ API で取得できるよう反映されていますので、ぜひ使ってみてください…！！🙏🙏</strong>
      </div>
    </div>
  </div>

  <div id="download" class="container mt-4 px-2 px-lg-3">
    <div class="card">
      <div class="card-body p-sm-4">
        <div class="download-form">
          <div class="form-group">
            <div class="input-group mb-1">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fas fa-broadcast-tower mr-2"></i>チャンネル</div>
              </div>
              <select id="channel-picker" class="custom-select h-100">
                <optgroup label="地デジ">
                  <option value="jk1">jk1: NHK総合</option>
                  <option value="jk2">jk2: NHK Eテレ </option>
                  <option value="jk4">jk4: 日本テレビ</option>
                  <option value="jk5">jk5: テレビ朝日</option>
                  <option value="jk6">jk6: TBSテレビ</option>
                  <option value="jk7">jk7: テレビ東京</option>
                  <option value="jk8">jk8: フジテレビ</option>
                  <option value="jk9">jk9: TOKYO MX</option>
                  <option value="jk10">jk10: テレ玉</option>
                  <option value="jk11">jk11: tvk</option>
                  <option value="jk12">jk12: チバテレビ</option>
                  <option value="jk13">jk13: サンテレビ</option>
                  <option value="jk14">jk14: KBS京都</option>
                </optgroup>
                <optgroup label="BS・CS">
                  <option value="jk101">jk101: NHK BS</option>
                  <option value="jk103">jk103: NHK BSプレミアム</option>
                  <option value="jk141">jk141: BS日テレ</option>
                  <option value="jk151">jk151: BS朝日</option>
                  <option value="jk161">jk161: BS-TBS</option>
                  <option value="jk171">jk171: BSテレ東</option>
                  <option value="jk181">jk181: BSフジ</option>
                  <option value="jk191">jk191: WOWOW PRIME</option>
                  <option value="jk192">jk192: WOWOW LIVE</option>
                  <option value="jk193">jk193: WOWOW CINEMA</option>
                  <option value="jk200">jk200: BS10</option>
                  <option value="jk201">jk201: BS10スターチャンネル</option>
                  <option value="jk211">jk211: BS11</option>
                  <option value="jk222">jk222: BS12 トゥエルビ</option>
                  <option value="jk236">jk236: BSアニマックス</option>
                  <option value="jk252">jk252: WOWOW PLUS</option>
                  <option value="jk260">jk260: BS松竹東急</option>
                  <option value="jk263">jk263: BSJapanext</option>
                  <option value="jk265">jk265: BSよしもと</option>
                  <option value="jk333">jk333: AT-X</option>
                </optgroup>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group date">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fas fa-calendar-alt mr-2"></i>日付</div>
              </div>
              <input id="datepicker-start" type="text" class="form-control datetimepicker-input" placeholder="2020/12/15" data-toggle="datetimepicker">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fas fa-clock mr-2"></i>時刻</div>
              </div>
              <input id="timepicker-start" type="text" class="form-control datetimepicker-input" placeholder="08:00:00" data-toggle="datetimepicker">
            </div>
          </div>
          <div class="form-group align-items-center justify-content-around">
            <div class="btn-group">
              <button id="time-minus30-button" type="button" class="btn btn-info">－30分</button>
              <button id="time-minus5-button" type="button" class="btn btn-success">－5分</button>
            </div>
            <button id="reflect-button" type="button" class="btn btn-outline-secondary">
              <svg class="download-form-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M207 477.5L12.7 283.1c-9.4-9.4-9.4-24.6 0-33.9l22.7-22.7c9.4-9.4 24.5-9.4 33.9 0l154.7 154 154.7-154c9.4-9.3 24.5-9.3 33.9 0l22.7 22.7c9.4 9.4 9.4 24.6 0 33.9L241 477.5c-9.4 9.3-24.6 9.3-34 0zm34-192L435.3 91.1c9.4-9.4 9.4-24.6 0-33.9l-22.7-22.7c-9.4-9.4-24.5-9.4-33.9 0L224 188.5 69.3 34.5c-9.4-9.3-24.5-9.3-33.9 0L12.7 57.2c-9.4 9.4-9.4 24.6 0 33.9L207 285.5c9.4 9.3 24.6 9.3 34 0z" class=""></path></svg>
            </button>
            <div class="btn-group">
              <button id="time-plus5-button" type="button" class="btn btn-success">＋5分</button>
              <button id="time-plus30-button" type="button" class="btn btn-info">＋30分</button>
            </div>
          </div>
          <div class="form-group mb-4">
            <div class="input-group date">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fas fa-calendar-alt mr-2"></i>日付</div>
              </div>
              <input id="datepicker-end" type="text" class="form-control datetimepicker-input" placeholder="2020/12/15" data-toggle="datetimepicker">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fas fa-clock mr-2"></i>時刻</div>
              </div>
              <input id="timepicker-end" type="text" class="form-control datetimepicker-input" placeholder="08:00:00" data-toggle="datetimepicker">
            </div>
          </div>
          <div class="action-button-group form-group mb-0 align-items-center justify-content-center">
            <button id="play-button" type="button" class="btn btn-danger mr-3"><i class="fas fa-external-link-alt mr-2"></i>過去ログビューアで再生</button>
            <button id="download-button" type="button" class="btn btn-primary mr-3"><i class="fas fa-download mr-2"></i>コメントを XML でダウンロード</button>
            <button id="urlopen-button" type="button" class="btn btn-secondary"><i class="fas fa-external-link-alt mr-2"></i>コメントの API URL を開く</button>
          </div>
        </div>
        <div class="modal fade" id="modal" tabindex="-1">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">エラー</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-2"></i>閉じる</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="about" class="container mt-4 px-2 px-lg-3">
    <div class="card">
      <h2 class="card-header font-weight-bold"><i class="fas fa-info-circle"></i>About</h2>
      <div class="card-body p-sm-4">
        <p>ニコニコ実況 過去ログ API は、ニコニコ実況の過去ログを XML や JSON データで提供しています。</p>
        <p>
          去る2020年12月、ニコニコ実況は<a href="https://blog.nicovideo.jp/niconews/143148.html" target="_blank">ニコニコ生放送内の一公式チャンネルとしてリニューアルされました。</a><br>これに伴い、2009年11月から運用されてきた旧システムは提供終了となり（事実上のサービス終了）、torne や BRAVIA などの家電への対応が軒並み終了する中、当時の生の声が詰まった約11年分の過去ログも同時に失われることとなってしまいました。
        </p>
        <p>
          そこで 5ch の DTV 板の住民が中心となり、旧ニコニコ実況が終了するまでに11年分の全チャンネルの過去ログをアーカイブする計画が立ち上がりました。紆余曲折あり Nekopanda 氏が約11年分のラジオや BS も含めた全チャンネルの過去ログを完璧に取得してくださったおかげで、11年分の過去ログが電子の海に消えていく事態は回避できました。<br>
          しかしながら、旧 API が廃止されてしまったため過去ログを API 経由で取得することができなくなり、またアーカイブされた過去ログから見たい範囲のログを探す場合も、アーカイブのサイズが合計約 150GB もあることから、とても以前のように手軽に過去ログに触れることはできなくなってしまいました。
        </p>
        <p>
          一方、ニコニコ生放送内の一公式チャンネルとして移行した新ニコニコ実況では、タイムシフト（旧ニコニコ実況での過去ログに相当）の視聴期限は3週間までとなっているため、その期限を過ぎると過去ログは視聴できなくなってしまいます。また一般会員は事前にタイムシフト予約をしておく必要があるなど、以前のような利便性は失われています。
        </p>
        <p class="mb-0">
          私たちは、ニコニコ実況に投稿された日本のテレビ放送についてのコメントは、当時の世相や時代背景を端的に表す、歴史的価値のある資料だと考えています。<br>
          この API は、ニコニコ実況のすべての過去ログを後世に残すべく、Nekopanda 氏が配布されていた旧ニコニコ実況の 2020/12/15 までのすべての過去ログに加え、コミュニティでの実況番組も含めた新ニコニコ実況、さらに 2024/06/10 からは実況用代替コメントサーバーである NX-Jikkyo の当日分の過去ログを5分に1回収集し、取得したデータを XML 形式や JSON 形式で提供する、非公式の過去ログデータベース API です。<br>
          比較的簡単に利用できるようにしているつもりですが、<span class="text-danger">いくつか注意事項もあります。</span>利用される際は下記の 機能・注意事項 をよく読んだ上でご利用ください。
        </p>
      </div>
    </div>
  </div>

  <div id="notes" class="container mt-4 px-2 px-lg-3">
    <div class="card">
      <h2 class="card-header font-weight-bold"><i class="fas fa-exclamation-circle"></i>機能・注意事項</h2>
      <div class="card-body p-sm-4">

        <ul class="mb-0">
          <li>2020/12/15 までに投稿された旧ニコニコ実況のすべての過去ログを取得できます。</li>
          <ul>
            <li>旧ニコニコ実況の過去ログデータには <a href="https://47.gigafile.nu/0214-h5003986d78e33dd116484423cfb5d981" target="_blank">かつて Nekopanda 氏が配布されていたもの (リンク切れ)</a> を利用しています。</li>
            <ul>
              <li>11年分の全チャンネルの過去ログを完璧に集めていただき感謝します。</li>
            </ul>
          </ul>
          <li>2020/12/16 以降に投稿された新ニコニコ実況のすべての過去ログを取得できます。</li>
          <ul>
            <li>新ニコニコ実況の過去ログデータは自作の <a href="https://github.com/tsukumijima/JKCommentCrawler" target="_blank">JKCommentCrawler</a> を利用して収集しています。</li>
            <li><s><span class="text-info">公式チャンネル ( jk1・jk2・jk4・jk5・jk6・jk7・jk8・jk9・jk101・jk211 ) の放送に加えて、公式では廃止され、現在は <a href="https://com.nicovideo.jp/community/co5117214" target="_blank">有志のコミュニティ</a> から放送されている NHK BS1・BS11 以外の BS・CS 各局 ( jk103・jk141・jk151・jk161・jk171・jk181・jk191・jk192・jk193・jk222・jk236・jk252・jk260・jk263・jk265・jk333 ) 、地デジ独立局 ( jk10・jk11・jk12 ) の過去ログも収集しています。</span></s></li>
            <ul>
              <li><a href="https://blog.nicovideo.jp/niconews/225559.html" target="_blank">ニコニコミュニティのサービス終了</a> にともない、今まで有志らのコミュニティで維持されてきたニコニコ実況チャンネルは、事実上 NX-Jikkyo 上に移行する形となっています。詳しく <a href="https://github.com/tsukumijima/JKCommentCrawler#%E3%81%84%E3%82%8F%E3%82%86%E3%82%8B%E3%82%B3%E3%83%9F%E3%83%A5%E3%83%8B%E3%83%86%E3%82%A3%E5%AE%9F%E6%B3%81%E3%81%A8-nx-jikkyo-%E3%81%AE%E9%96%A2%E4%BF%82%E3%81%AB%E3%81%A4%E3%81%84%E3%81%A6" target="_blank">こちらの記事</a> をご覧ください。</li>
            </ul>
            <li>5分に1回、当日分の全チャンネルの過去ログを自動で収集します。</li>
            <ul>
              <li>収集は5分に1回のため、<span class="text-danger">たとえば 17:02 に終わった番組の過去ログを直後の 17:03 に取得する、といったことはできません。</span></li>
              <li>17:00 ～ 17:05 の過去ログの収集が終わる 17:05 以降（実際は収集に 3 分ほどかかるため 17:08 以降）まで待つ必要があります。</li>
            </ul>
            <li><span class="text-danger">レスポンスには運営コメント（ <code>/nicoad</code> や <code>/emotion</code> のようなコマンド付きコメント）も含まれます。</span></li>
            <ul>
              <li>運営コメントをレスポンスに含めるべきかかなり悩みましたが、元データの段階で運営コメントを取り除いてしまうと後から運営コメントが必要になっても手遅れになってしまうので、それよりかはいいかなーと判断しました。</li>
              <li>このため、API を利用するクライアントソフト側で運営コメントをすべて弾いたり、<code>/nicoad</code> からメッセージだけ取り出して固定コメントとして描画したり…といった実装が別途必要になります。</li>
              <li>正規表現なら <code>/\/[a-z]+ /</code> で判定できると思います。</li>
              <li>メッセージサーバーの仕様が変更された 2024/08/05 以降の新ニコニコ実況では、コメント配信形式が Protocol Buffers で構造化された関係で、「運営コメント」という概念自体が廃止されています。(それ以前の過去ログには運営コメントが記録されているため、運営コメントの除外処理自体は今後も必要です。)</li>
            </ul>
          </ul>
          <li>2024/06/10 以降に投稿された <a href="https://nx-jikkyo.tsukumijima.net/" target="_blank">NX-Jikkyo</a> のすべての過去ログを取得できます。</li>
          <ul>
            <li><span class="text-info">NX-Jikkyo は、<a class="link" href="https://blog.nicovideo.jp/niconews/225099.html" target="_blank">サイバー攻撃の影響で 2024/06/08 ~ 08/05 まで鯖落ちしていた</a> ニコニコ実況に代わる、ニコニコ実況民のための避難所であり、<a class="link" href="https://github.com/tsukumijima/NX-Jikkyo/blob/master/server/app/routers/websocket.py" target="_blank">ニコニコ生放送互換の WebSocket API</a> を備えるコメントサーバーです。</span></li>
            <li>当時ニコニコ全体のサーバーダウンの長期化が見込まれたことから (実際完全復旧まで3ヶ月弱を要した) 「ニコニコ実況 過去ログ API」の運営者が突貫で開発し、2024/06/10 から運営しています。</li>
            <li>ニコニコ実況が復旧した現在では、「ニコニコ実況の Web 版非公式コメントビューア」＋「公式にない実況チャンネルを補完するコメントサーバー」として運営を続けています。</li>
            <li>NX-Jikkyo のコメントデータは極力ニコニコ生放送準拠のフォーマットで保存されているほか、統合にあたり従来のニコニコ実況の過去ログ同様の XML に変換した上で保存しています。この過去ログ API からは、ニコニコ実況のコメントと全く同じように取得できます。</li>
          </ul>
          <li><span class="text-danger">指定された期間の過去ログが存在しない場合は空の packet が返されます。</span></li>
          <ul>
            <li>たとえば 2009/11/26（ニコニコ実況のリリース日）よりも前の時刻などです。また、新しく開局したチャンネルで、開局前の時刻を指定したときも同じく空の packet が返されます。</li>
            <li>これ以外にも、指定された日付のコメント自体は存在するが、指定された時刻で絞り込むとその期間内にコメントが 1 件も投稿されていなかった、といった場合にも発生します（コメントの少ない早朝や昼間、BS チャンネルに多い）。</li>
            <li>具体的には、XML なら <code>&lt;packet&gt;&lt;/packet&gt;</code> 、JSON なら <code>{"packet": []}</code> のようなレスポンスになります。</li>
            <li>エラーにはならないので、もし 1 件も過去ログを取得できなかった場合にエラーにしたい場合は、適宜 API クライアント側で 1 件でもコメントを取得できているか確認するような実装にしてください。</li>
          </ul>
          <li><span class="text-info">レスポンスの 文字コードは UTF-8 (BOM なし) 、改行コードは LF です。</span>ツール等で利用する際は注意してください。</li>
          <li>3日分を超えるコメントを一度に取得することはできません。数日分かに分けて取得するようにしてください。</li>
          <li>万全は期しているつもりですが、突貫工事で作ったため修正できていない不具合があるかもしれません。</li>
          <li><span class="text-danger">一個人が運営している非公式 API です。ニコニコ公式とは一切関係ありません。</span></li>
          <li>過去ログデータを除いたコードは <a href="https://github.com/tsukumijima/jikkyo-api" target="_blank">GitHub</a> にて公開しています。なにか不具合があれば <a href="https://github.com/tsukumijima/jikkyo-api/issues" target="_blank">Issues</a> へお願いします。</li>
          <ul>
            <li>未検証ですが、自分のサイトでこの API をホストすることも可能です。</li>
          </ul>
          <li>5分おきに収集した過去ログデータは <a href="https://huggingface.co/datasets/KakologArchives/KakologArchives" target="_blank">KakologArchives</a> (Hugging Face) にて公開しています。</li>
          <ul>
            <li>この API も KakologArchives リポジトリから過去ログデータを取得しています。</li>
            <li>以前はサーバーのローカルディスクに保存された過去ログデータを出力していましたが、サーバーのディスク容量を 170GB 近く消費することから自宅サーバーで運用せざるを得ず、安定性に問題を抱えていました。</li>
            <li>Hugging Face から過去ログデータを取得するように変更し、API サーバーをクラウドに置けるようになったことで、以前よりも安定性が向上しています。</li>
          </ul>
        </ul>

      </div>
    </div>
  </div>

  <div id="request-parameter" class="container mt-4 mb-4 px-2 px-lg-3">
    <div class="card">
      <h2 class="card-header font-weight-bold"><i class="fas fa-paper-plane"></i>リクエストパラメータ</h2>
      <div class="card-body p-sm-4 pb-4">
        <div>
          <p>
            データをリクエストする際のベースとなる URL は以下になります。<br>
            <span style="color:#d00;"><strong>{{ config('app.url') }}/api/kakolog/{実況ID}</strong></span><br>
            この URL に下の表のパラメータを加え、実際にリクエストします。
          </p>

          <table class="table table-request">
            <tr>
              <th class="title" nowrap>パラメータ名</th>
              <th class="title">説明</th>
            </tr>
            <tr>
              <th>{実況ID}</th>
              <td>
                ニコニコ実況のチャンネル ID を表します。URL 自体に含めてください。<br>
                例: NHK総合 → jk1・BS11 → jk211<br>
              </td>
            </tr>
            <tr>
              <th>starttime</th>
              <td>
                取得を開始する時刻の UNIX タイムスタンプを表します。<br>
              </td>
            </tr>
            <tr>
              <th>endtime</th>
              <td>
                取得を終了する時刻の UNIX タイムスタンプを表します。<br>
              </td>
            </tr>
            <tr>
              <th>format</th>
              <td>
                出力するフォーマットを表します。xml（XML 形式）または json（JSON 形式）のいずれかを指定します。<br>
                XML 形式では過去ログをヘッダーをつけた上でそのまま出力します。<br>
                JSON 形式では過去ログをニコニコ動画のコメント API のレスポンスと類似した形態の JSON 形式に変換して出力します。<br>
              </td>
            </tr>
          </table>

          <div class="column d-inline-block px-4 py-3" style="border: 1px solid #dee2e6; width: 100%;">
            <strong>（例）「 2020/11/27 08:00:00 ～ 2020/11/27 08:15:00 の NHK総合の XML 形式のコメント」を取得する場合</strong><br>
            <div>
              下記 URL にアクセスしてデータを取得します。<br>
              基本 URL + 2020/11/27 08:00:00 のタイムスタンプ + 2020/11/27 08:15:00 のタイムスタンプ + フォーマット (xml)<br>
              UNIX タイムスタンプの計算は <a href="https://tool.konisimple.net/date/unixtime" target="_blank">Unixtime相互変換ツール</a> のサイトが使いやすいです。
            </div>
            <a style="margin-left: 30px;" href="{{ config('app.url') }}/api/kakolog/jk1?starttime=1606431600&endtime=1606432500&format=xml" target="_blank">
              {{ config('app.url') }}/api/kakolog/jk1?starttime=1606431600&endtime=1606432500&format=xml
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="response-field" class="container mt-4 px-2 px-lg-3">
    <div class="card">
      <h2 class="card-header font-weight-bold"><i class="fas fa-reply"></i>レスポンスフィールド</h2>
      <div class="card-body p-sm-4 pb-4">

      <p>
        取得した XML・JSON データは以下の定義に基づいて構成されています。（プロパティ名は順不同）<br>
        文字コードは UTF-8（BOMなし）、改行コードは LF です。
      </p>

        <table class="table table-response mt-4" cellpadding="0" cellspacing="0" class="normal">
          <tr>
            <th class="title" nowrap>プロパティ名</th>
            <th class="title">内容</th>
          </tr>
          <tr>
            <th class="thline">packet</th>
            <td class="tdline">
              <div style="margin-bottom: 12px;">すべてのコメントデータがくるまれている親要素。</div>
              <table cellpadding="0" cellspacing="0" width="100%" class="tableline  table-response">
                <tr>
                  <th class="title" nowrap>プロパティ名</th>
                  <th class="title" width="98%">内容</th>
                </tr>
                <tr>
                  <th class="thline">chat</th>
                  <td class="tdline">
                    <div style="margin-bottom: 12px;">
                      コメントデータ。<br>
                      取得した過去ログをそのまま出力しているため、一部のコメントにしか存在しないプロパティもあります。<br>
                    </div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="tableline  table-response">
                      <tr>
                        <th class="title" nowrap>プロパティ名</th>
                        <th class="title" width="98%">内容</th>
                      </tr>
                      <tr>
                        <th class="thline">thread</th>
                        <td class="tdline">コメントのスレッド ID</td>
                      </tr>
                      <tr>
                        <th class="thline">no</th>
                        <td class="tdline">
                          コメント番号 (コメ番)　コメ番の単調増加や一意性は保証されていないため、<br>
                          ソートには date + date_usec の利用を推奨
                        </td>
                      </tr>
                      <tr>
                        <th class="thline">vpos</th>
                        <td class="tdline">スレッド ID から起算したコメントの再生位置 (1/100秒)</td>
                      </tr>
                      <tr>
                        <th class="thline">date</th>
                        <td class="tdline">コメント投稿時間の UNIX タイムスタンプ</td>
                      </tr>
                      <tr>
                        <th class="thline">date_usec</th>
                        <td class="tdline">
                          コメント投稿時間 UNIX タイムスタンプの小数点以下の時間 (マイクロ秒単位)<br>
                          コメント投稿時間の正確なタイムスタンプは<br>
                          date: 1606431600 / date_usec: 257855 なら 1606431600.257855 になる</td>
                      </tr>
                      <tr>
                        <th class="thline">user_id</th>
                        <td class="tdline">
                          ユーザー ID (コマンドに 184 が指定されている場合は匿名化される)<br>
                          NX-Jikkyo に投稿されたコメントのユーザー ID は必ず 35 文字以上になる
                        </td>
                      </tr>
                      <tr>
                        <th class="thline">mail</th>
                        <td class="tdline">コメントのコマンド (184, red naka big など)</td>
                      </tr>
                      <tr>
                        <th class="thline">premium</th>
                        <td class="tdline">コメントしたユーザーがプレミアム会員であれば 1</td>
                      </tr>
                      <tr>
                        <th class="thline">anonymity</th>
                        <td class="tdline">匿名コメントであれば 1</td>
                      </tr>
                      <tr>
                        <th class="thline">nx_jikkyo</th>
                        <td class="tdline">
                            NX-Jikkyo に投稿されたコメントであれば 1 (過去ログ API 独自のフィールド)<br>
                            ニコニコ実況に投稿されたコメントでは省略される<br>
                        </td>
                      </tr>
                      <tr>
                        <th class="thline">content</th>
                        <td class="tdline">
                          コメント本文 (XML 形式では chat 要素自体の値)<br>
                          AA など、まれに複数行コメントがあるので注意<br>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <th class="thline">error</th>
            <td class="tdline">
              エラーメッセージ（エラー発生時のみ）。指定されたフォーマットに合わせて出力されますが、<br>
              存在しないフォーマットが指定されたりパラメータが不足している場合には常に JSON 形式で出力されます。<br>
            </td>
          </tr>
        </table>

        <table class="table table-response, table-response-error">
          <tr>
            <th class="title" nowrap>エラーメッセージ</th>
            <th class="title">説明</th>
          </tr>
          <tr>
            <th>必要なパラメータが存在しません。</th>
            <td>
              リクエストパラメータのうちのいずれかが欠けているときに発生します。<br>
              常に JSON 形式で出力されます。<br>
            </td>
          </tr>
          <tr>
            <th>フォーマットは XML または JSON 形式である必要があります。</th>
            <td>
              リクエストパラメータに設定されたフォーマットが xml でも json でもないときに発生します。<br>
              常に JSON 形式で出力されます。<br>
            </td>
          </tr>
          <tr>
            <th>指定された実況 ID は存在しません。</th>
            <td>
              指定された実況 ID が存在しないときに発生します。<br>
              過去一度も存在したことがない実況 ID のみが対象のため、新ニコニコ実況では用意されていないラジオや BS の一部チャンネルを指定した際はこのエラーは発生しません。<br>
            </td>
          </tr>
          <tr>
            <th>取得開始時刻または取得終了時刻が不正です。</th>
            <td>
              取得開始時刻・取得終了時刻の UNIX タイムスタンプがマイナスの値や現在時刻より未来の値になっているときに発生します。<br>
            </td>
          </tr>
          <tr>
            <th>取得開始時刻と取得終了時刻が同じ時刻です。</th>
            <td>
              取得開始時刻と取得終了時刻の UNIX タイムスタンプが同じ値のときに発生します。<br>
            </td>
          </tr>
          <tr>
            <th>指定された取得開始時刻は取得終了時刻よりも後です。</th>
            <td>
              取得開始時刻の UNIX タイムスタンプが取得終了時刻の UNIX タイムスタンプよりも大きいときに発生します。<br>
            </td>
          </tr>
          <tr>
            <th>3日分を超えるコメントを一度に取得することはできません。数日分かに分けて取得するようにしてください。</th>
            <td>
              サーバーの処理負荷などの兼ね合いにより、3日分を超えるコメントを一度に取得することはできません。数日分かに分けて取得するようにしてください。<br>
            </td>
          </tr>
          <tr>
            <th>Hugging Face で障害が発生しているため、過去ログを取得できません。(HTTP Error xxx)</th>
            <td>
              障害が発生しているなどの理由により、Hugging Face のサーバーにアクセスできなかったときに起きるエラーです。<br>
              出力元の過去ログデータは Hugging Face から取得しているため、Hugging Face で障害が発生している間は過去ログを取得できません。<br>
            </td>
          </tr>
        </table>

      </div>
    </div>
  </div>

  <div id="xml-data-sample" class="container mt-4 mb-4 pb-4 pb-sm-0 px-2 px-lg-3">
    <div class="card">
      <h2 class="card-header font-weight-bold"><i class="fas fa-code"></i>XML データサンプル</h2>
      <div class="card-body p-sm-4">

        <p class="mb-4">
          XML は指定された期間の過去ログをそのまま出力しているため、必ずしも Valid な XML であるとは限りません（まれに破損している場合がある）。<br>
        </p>

        <pre>&lt;packet&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2750&quot; vpos=&quot;1440040&quot; date=&quot;1606431601&quot; mail=&quot;184&quot; user_id=&quot;mmJyd4lCsV6e3loLXR0QvZnlnFI&quot; premium=&quot;1&quot; anonymity=&quot;1&quot; date_usec=&quot;373180&quot;&gt;六甲おろし歌って&lt;/chat&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2751&quot; vpos=&quot;1440136&quot; date=&quot;1606431602&quot; mail=&quot;184&quot; user_id=&quot;Vz1E1ii0OXV1ApWddfG7niOSYak&quot; anonymity=&quot;1&quot; date_usec=&quot;183595&quot;&gt;ｷﾀ━━━━(ﾟ∀ﾟ)━━━━!!&lt;/chat&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2752&quot; vpos=&quot;1440100&quot; date=&quot;1606431603&quot; mail=&quot;184&quot; user_id=&quot;HCnCAmVDEac_T_fkeS9EHkymli8&quot; anonymity=&quot;1&quot; date_usec=&quot;405333&quot;&gt;ｈｊｍｔ&lt;/chat&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2753&quot; vpos=&quot;1440298&quot; date=&quot;1606431603&quot; mail=&quot;184&quot; user_id=&quot;SxULPQ3aPP4noCUEGj_1GOEjp8Y&quot; anonymity=&quot;1&quot; date_usec=&quot;965862&quot;&gt;完全版はBSで方式やろな&lt;/chat&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2754&quot; vpos=&quot;1440400&quot; date=&quot;1606431605&quot; mail=&quot;184&quot; user_id=&quot;2H54YZyR0BLlv8_1XnlYl-euia4&quot; anonymity=&quot;1&quot; date_usec=&quot;103550&quot;&gt;合唱会&lt;/chat&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2755&quot; vpos=&quot;1440400&quot; date=&quot;1606431605&quot; mail=&quot;184&quot; user_id=&quot;0ojZYR0_KDaecXFZGnaqwazTU3w&quot; premium=&quot;1&quot; anonymity=&quot;1&quot; date_usec=&quot;540295&quot;&gt;コンサート&lt;/chat&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2756&quot; vpos=&quot;1440400&quot; date=&quot;1606431605&quot; mail=&quot;184&quot; user_id=&quot;FREZGJEF5OhEaGskb3upsxbxu2c&quot; anonymity=&quot;1&quot; date_usec=&quot;585768&quot;&gt;らすとか&lt;/chat&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2757&quot; vpos=&quot;1440404&quot; date=&quot;1606431606&quot; mail=&quot;184&quot; user_id=&quot;JknVYfrFwBy2CDrz_jz8bWb5-hU&quot; premium=&quot;1&quot; anonymity=&quot;1&quot; date_usec=&quot;83051&quot;&gt;！？&lt;/chat&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2758&quot; vpos=&quot;1440515&quot; date=&quot;1606431606&quot; mail=&quot;184&quot; user_id=&quot;QrzHcVSABkD_JaPWmNzcXYBlzUY&quot; anonymity=&quot;1&quot; date_usec=&quot;782894&quot;&gt;コロナ禍じゃ無かったら結構許されないよな&lt;/chat&gt;
&lt;chat thread=&quot;1606417201&quot; no=&quot;2759&quot; vpos=&quot;1440803&quot; date=&quot;1606431609&quot; mail=&quot;184&quot; user_id=&quot;CrwzC_JXPIjjPIBW27W1QVtUc80&quot; anonymity=&quot;1&quot; date_usec=&quot;16461&quot;&gt;ハンケチ用意&lt;/chat&gt;
（以下コメントが続く）
&lt;/packet&gt;</pre>

      </div>
    </div>
  </div>

  <div id="json-data-sample" class="container mt-4 mb-4 pb-4 pb-sm-0 px-2 px-lg-3">
    <div class="card">
      <h2 class="card-header font-weight-bold"><i class="fas fa-code"></i>JSON データサンプル</h2>
      <div class="card-body p-sm-4">

        <p class="mb-4">
          ASCII の範囲外の文字もエスケープされずに出力されます。<br>
          実際のレスポンスではサイズが大きくなってしまうため、下記のような改行やインデントは行われません。<br>
        </p>

        <pre>{
    "packet": [
        {
            "chat": {
                "thread": "1606417201",
                "no": "2750",
                "vpos": "1440040",
                "date": "1606431601",
                "mail": "184",
                "user_id": "mmJyd4lCsV6e3loLXR0QvZnlnFI",
                "premium": "1",
                "anonymity": "1",
                "date_usec": "373180",
                "content": "六甲おろし歌って"
            }
        },
        {
            "chat": {
                "thread": "1606417201",
                "no": "2751",
                "vpos": "1440136",
                "date": "1606431602",
                "mail": "184",
                "user_id": "Vz1E1ii0OXV1ApWddfG7niOSYak",
                "anonymity": "1",
                "date_usec": "183595",
                "content": "ｷﾀ━━━━(ﾟ∀ﾟ)━━━━!!"
            }
        },
        {
            "chat": {
                "thread": "1606417201",
                "no": "2752",
                "vpos": "1440100",
                "date": "1606431603",
                "mail": "184",
                "user_id": "HCnCAmVDEac_T_fkeS9EHkymli8",
                "anonymity": "1",
                "date_usec": "405333",
                "content": "ｈｊｍｔ"
            }
        },
        {
            "chat": {
                "thread": "1606417201",
                "no": "2753",
                "vpos": "1440298",
                "date": "1606431603",
                "mail": "184",
                "user_id": "SxULPQ3aPP4noCUEGj_1GOEjp8Y",
                "anonymity": "1",
                "date_usec": "965862",
                "content": "完全版はBSで方式やろな"
            }
        },
        {
            "chat": {
                "thread": "1606417201",
                "no": "2754",
                "vpos": "1440400",
                "date": "1606431605",
                "mail": "184",
                "user_id": "2H54YZyR0BLlv8_1XnlYl-euia4",
                "anonymity": "1",
                "date_usec": "103550",
                "content": "合唱会"
            }
        },
        {
            "chat": {
                "thread": "1606417201",
                "no": "2755",
                "vpos": "1440400",
                "date": "1606431605",
                "mail": "184",
                "user_id": "0ojZYR0_KDaecXFZGnaqwazTU3w",
                "premium": "1",
                "anonymity": "1",
                "date_usec": "540295",
                "content": "コンサート"
            }
        },
        {
            "chat": {
                "thread": "1606417201",
                "no": "2756",
                "vpos": "1440400",
                "date": "1606431605",
                "mail": "184",
                "user_id": "FREZGJEF5OhEaGskb3upsxbxu2c",
                "anonymity": "1",
                "date_usec": "585768",
                "content": "らすとか"
            }
        },
        {
            "chat": {
                "thread": "1606417201",
                "no": "2757",
                "vpos": "1440404",
                "date": "1606431606",
                "mail": "184",
                "user_id": "JknVYfrFwBy2CDrz_jz8bWb5-hU",
                "premium": "1",
                "anonymity": "1",
                "date_usec": "83051",
                "content": "！？"
            }
        },
        {
            "chat": {
                "thread": "1606417201",
                "no": "2758",
                "vpos": "1440515",
                "date": "1606431606",
                "mail": "184",
                "user_id": "QrzHcVSABkD_JaPWmNzcXYBlzUY",
                "anonymity": "1",
                "date_usec": "782894",
                "content": "コロナ禍じゃ無かったら結構許されないよな"
            }
        },
        {
            "chat": {
                "thread": "1606417201",
                "no": "2759",
                "vpos": "1440803",
                "date": "1606431609",
                "mail": "184",
                "user_id": "CrwzC_JXPIjjPIBW27W1QVtUc80",
                "anonymity": "1",
                "date_usec": "16461",
                "content": "ハンケチ用意"
            }
        },
        （以下コメントが続く）
    ]
}</pre>

      </div>
    </div>
  </div>

  <footer id="footer" class="footer bg-dark">
    <div class="container d-flex flex-column align-items-center align-items-sm-end pt-3 pb-3">
      <div class="d-inline text-white text-center text-sm-right">
        <a class="mr-1">© 2020 - {{ date('Y') }}</a>
        <br class="d-inline d-sm-none">
        ニコニコ実況 過去ログ API
      </div>
    </div>
  </footer>

</body>
</html>
