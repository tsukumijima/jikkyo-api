<!DOCTYPE html>
<html lang="ja">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ニコニコ実況 過去ログ API</title>
  <link rel="icon" type="image/png" href="{{ url('/') }}/logo.png">
  <link rel="canonical" href="{{ env('APP_URL') }}/">

  <!-- OGP -->
  <meta property="og:type" content="article">
  <meta property="description" content="ニコニコ実況の過去ログを XML や JSON データで返す非公式 API です。">
  <meta property="og:description" content="ニコニコ実況の過去ログを XML や JSON データで返す非公式 API です。">
  <meta property="og:title" content="ニコニコ実況 過去ログ API">
  <meta property="og:image" content="{{ url('/') }}/logo.png">
  <meta property="og:locale" content="ja_JP">
  <!-- /OGP -->

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary">
  <meta name="twitter:description" content="ニコニコ実況の過去ログを XML や JSON データで返す非公式 API です。">
  <meta name="twitter:title" content="ニコニコ実況 過去ログ API">
  <meta name="twitter:image" content="{{ url('/') }}/logo.png">
  <!-- /Twitter Card -->

  <!-- JavaScript -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script type="text/javascript" src="{{ url('/') }}/script.js"></script>

  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.13.1/css/all.css">
  <link rel="stylesheet" type="text/css" href="{{ url('/') }}/style.css">

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('APP_GTAG') }}"></script>
  <script>

    window.dataLayer = window.dataLayer || [];
    function gtag(){
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', '{{ env('APP_GTAG') }}');

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
      <a class="navbar-brand text-white" href="./">
        <img src="{{ url('/') }}/logo.png" class="d-inline-block align-top">
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

  <div id="about" class="container mt-4 px-2 px-lg-3">
    <div class="card">
      <h2 class="card-header font-weight-bold"><i class="fas fa-info-circle"></i>About</h2>
      <div class="card-body p-sm-4">

        <p>ニコニコ実況 過去ログ APIは、ニコニコ実況の過去ログを XML や JSON データで提供する非公式 API です。</p>
        <p class="mb-0">（準備中）</p>
        
      </div>
    </div>
  </div>

  <div id="notes" class="container mt-4 px-2 px-lg-3">
    <div class="card">
      <h2 class="card-header font-weight-bold"><i class="fas fa-exclamation-circle"></i>注意事項</h2>
      <div class="card-body p-sm-4">

        <ul class="mb-0">
          <li>レスポンスの 文字コードは UTF-8（BOMなし）、改行コードは LF です。ツール等で利用する際は注意してください。</li>
          <li>3日分を超えるコメントを一度に取得することはできません。数日分かに分けて取得するようにしてください。</li>
          <li>万全は期しているつもりですが、1日半突貫工事で作ったので修正できていない不具合があるかもしれません。</li>
          <li>一個人が運営している非公式 API です。ニコニコ公式とは一切関係ありません。</li>
          <li>コメントデータを除いたコードは <a href="https://github.com/tsukumijima/jikkyo-api" target="_blank">GitHub</a> にて公開しています。なにか不具合があれば <a href="https://github.com/tsukumijima/jikkyo-api/issues" target="_blank">Issues</a> へお願いします。</li>
          <ul>
            <li>未検証ですが、自分のサイトでこの API をホストすることも可能です。</li>
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
            <span style="color:#d00;"><strong>{{ url('/') }}/api/kakolog/{実況ID}</strong></span><br>
            この URL に下の表のパラメータを加え、実際にリクエストします。
          </p>
          
          <table class="table">
            <tr>
              <th class="title" nowrap>パラメータ名</th>
              <th class="title">説明</th>
            </tr>
            <tr>
              <th>{実況ID}</th>
              <td>
                ニコニコ実況のチャンネル ID を表します。URL 自体に含めてください。<br>
                例: NHK総合 → jk1・BS11 → jk211
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
                出力するフォーマットを表します。xml（ XML 形式）または json（ JSON 形式）のいずれかを指定します。<br>
                XML 形式では過去ログをヘッダーをつけた上でそのまま出力します。<br>
                JSON 形式では過去ログをニコニコ動画のコメント API のレスポンスと類似した形態の JSON 形式に変換して出力します。<br>
              </td>
            </tr>
          </table>
          
          <div class="column d-inline-block px-4 py-3" style="border: 1px solid #dee2e6; width: 100%;">
            <strong>（例）「 2020/11/27 08:00:00 ～ 2020/11/27 08:15:00 の NHK総合の XML 形式のコメント」を取得する場合</strong><br>
            <div>
              下記 URL にアクセスしてデータを取得します。<br>
              基本 URL + 2020/11/27 08:00:00 のタイムスタンプ + 2020/11/27 08:15:00 のタイムスタンプ + フォーマット (xml)
            </div>
            <a href="{{ url('/') }}/api/kakolog/jk1?starttime=1606431600&endtime=1606432500&format=xml" target="_blank">
              {{ url('/') }}/api/kakolog/jk1?starttime=1606431600&endtime=1606432500&format=xml
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
  
        <table class="table mt-4" cellpadding="0" cellspacing="0" class="normal">
          <tr>
            <th class="title" nowrap>プロパティ名</th>
            <th class="title">内容</th>
          </tr>
          <tr>
            <th class="thline">packet</th>
            <td class="tdline">
              <div style="margin-bottom: 12px;">全てのコメントデータがくるまれている親要素</div>
              <table cellpadding="0" cellspacing="0" width="100%" class="tableline">
                <tr>
                  <th class="title" nowrap>プロパティ名</th>
                  <th class="title" width="98%">内容</th>
                </tr>
                <tr>
                  <th class="thline">chat</th>
                  <td class="tdline">
                    <div style="margin-bottom: 12px;">
                      コメントデータ<br>
                      過去ログをそのまま出力しているため、一部のコメントにしか存在しないプロパティもあります<br>
                    </div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="tableline">
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
                        <td class="tdline">コメント番号（コメ番）</td>
                      </tr>
                      <tr>
                        <th class="thline">vpos</th>
                        <td class="tdline">スレッド ID から起算したコメントの再生位置（1/100秒）</td>
                      </tr>
                      <tr>
                        <th class="thline">date</th>
                        <td class="tdline">コメント投稿時間の UNIX タイムスタンプ</td>
                      </tr>
                      <tr>
                        <th class="thline">date_usec</th>
                        <td class="tdline">
                          コメント投稿時間の小数点以下の時間　コメント投稿時間の正確なタイムスタンプは<br>
                          date: 1606431600・date_usec: 257855 なら 1606431600.257855 のようになる</td>
                      </tr>
                      <tr>
                        <th class="thline">user_id</th>
                        <td class="tdline">ユーザー ID（コマンドに 184 が指定されている場合は匿名化される）</td>
                      </tr>
                      <tr>
                        <th class="thline">mail</th>
                        <td class="tdline">コメントのコマンド（184, red naka big など）</td>
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
                        <th class="thline">content</th>
                        <td class="tdline">
                          コメント本文（ XML 形式では chat 要素自体の値）<br>
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
              エラーメッセージ（エラー発生時のみ）　指定されたフォーマットに合わせて出力されますが、<br>
              存在しないフォーマットが指定されたりパラメータが不足している場合には常に JSON 形式で出力されます<br>
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
