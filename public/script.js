
$(function() {

    // 現在時刻
    const date = moment().set('minute', 0).set('second', 0);

    // チャンネル
    let channel = 'jk1';

    // 取得開始時刻
    let starttime = date;

    // 取得終了時刻
    let endtime = date;

    // 取得開始時刻の日付ピッカー
    $('#datepicker-start').datetimepicker({
        dayViewHeaderFormat: 'YYYY年MM月',
        format: 'YYYY/MM/DD',
        locale: 'ja',
        defaultDate: date,
        maxDate: date,
    });
    
    // 取得開始時刻の時刻ピッカー
    $('#timepicker-start').datetimepicker({
        dayViewHeaderFormat: 'HH:mm:ss',
        format: 'HH:mm:ss',
        locale: 'ja',
        defaultDate: date,
    });

    // 取得終了時刻の日付ピッカー
    $('#datepicker-end').datetimepicker({
        dayViewHeaderFormat: 'YYYY年MM月',
        format: 'YYYY/MM/DD',
        locale: 'ja',
        defaultDate: date,
        maxDate: date,
    });
    
    // 取得終了時刻の時刻ピッカー
    $('#timepicker-end').datetimepicker({
        dayViewHeaderFormat: 'HH:mm:ss',
        format: 'HH:mm:ss',
        locale: 'ja',
        defaultDate: date,
    });

    // チャンネルのフォームが変化したとき
    $('#channel-picker').on('change', (event) => {
        channel = $('#channel-picker').val();
    });
    
    // 取得開始時刻のフォームが変化したとき
    $('#datepicker-start, #timepicker-start').on('change.datetimepicker', (event) => {
        const datepicker_start = $('#datepicker-start').val();
        const timepicker_start = $('#timepicker-start').val();
        const datetimepicker_start = moment(`${datepicker_start} ${timepicker_start}`, 'YYYY/MM/DD HH:mm:ss');
        starttime = datetimepicker_start;
    });
    
    // 取得終了時刻のフォームが変化したとき
    $('#datepicker-end, #timepicker-end').on('change.datetimepicker', (event) => {
        const datepicker_end = $('#datepicker-end').val();
        const timepicker_end = $('#timepicker-end').val();
        const datetimepicker_end = moment(`${datepicker_end} ${timepicker_end}`, 'YYYY/MM/DD HH:mm:ss');
        endtime = datetimepicker_end;
    });

    // 反映ボタンがクリックされたとき
    $('#reflect-button').click((event) => {
        const datepicker_start = $('#datepicker-start').val();
        const timepicker_start = $('#timepicker-start').val();
        $('#datepicker-end').val(datepicker_start);
        $('#timepicker-end').val(timepicker_start);
        const datetimepicker_end = moment(`${datepicker_start} ${timepicker_start}`, 'YYYY/MM/DD HH:mm:ss');
        endtime = datetimepicker_end;
    });

    // -30分ボタンがクリックされたとき
    $('#time-minus30-button').click((event) => {
        endtime = endtime.subtract(30, 'minutes');
        $('#datepicker-end').val(endtime.format('YYYY/MM/DD'));
        $('#timepicker-end').val(endtime.format('HH:mm:ss'));
    });

    // -5分ボタンがクリックされたとき
    $('#time-minus5-button').click((event) => {
        endtime = endtime.subtract(5, 'minutes');
        $('#datepicker-end').val(endtime.format('YYYY/MM/DD'));
        $('#timepicker-end').val(endtime.format('HH:mm:ss'));
    });

    // +5分ボタンがクリックされたとき
    $('#time-plus5-button').click((event) => {
        endtime = endtime.add(5, 'minutes');
        $('#datepicker-end').val(endtime.format('YYYY/MM/DD'));
        $('#timepicker-end').val(endtime.format('HH:mm:ss'));
    });

    // +30分ボタンがクリックされたとき
    $('#time-plus30-button').click((event) => {
        endtime = endtime.add(30, 'minutes');
        $('#datepicker-end').val(endtime.format('YYYY/MM/DD'));
        $('#timepicker-end').val(endtime.format('HH:mm:ss'));
    });

    // API URL を作成してエラーがないかチェック
    function checkAPI() {

        // Promise を返す
        return new Promise((resolve, reject) => {
        
            // API URL を構築
            const api_url = `${window.location.origin}/api/kakolog/${channel}?starttime=${starttime.unix()}&endtime=${endtime.unix()}&format=xml`;

            // エラーが出ないか確認する
            $.ajax({
                url: api_url.replace('xml', 'json'),
            // リクエスト成功
            }).done((response, textStatus, jqXHR) => {
                if (response.error) {  // エラーがあれば
                    // モーダルにエラーを表示
                    $('#modal .modal-body').html(response.error);
                    $('#modal').modal();
                    reject(response.error);
                } else {
                    if (response.packet.length === 0) {
                        $('#modal .modal-body').html('指定された期間の過去ログは存在しません。');
                        $('#modal').modal();
                        reject('指定された期間の過去ログは存在しません。');
                    } else {
                        resolve(api_url);  // エラーなし
                    }
                }
            // リクエスト失敗
            }).fail((jqXHR, textStatus, errorThrown) => {
                let error;
                switch (jqXHR.status) {
                    // 500 Internal Server Error
                    case 500:
                        error = 'サーバーエラーが発生しました。過去ログ API に不具合がある可能性があります。(HTTP Error 500)';
                    break;
                    // 503 Service Unavailable
                    case 500:
                        error = '現在、過去ログ API は一時的に利用できなくなっています。(HTTP Error 503)';
                    break;
                    // その他のステータスコード
                    default:
                        error = '不明なエラーが発生しました。';
                    break;
                }
                // モーダルにエラーを表示
                $('#modal .modal-body').html(error + `<br>API URL: <a href="${api_url}" target="_blank">${api_url}</a>`);
                $('#modal').modal();
                reject(error);
            });
        });
    }

    // ダウンロードボタンがクリックされたとき
    $('#download-button').click((event) => {

        // エラーがなければ
        checkAPI().then((api_url) => {
    
            // 仮想の a 要素を作成してダウンロードさせる
            const link = document.createElement('a');
            link.href = api_url;
            link.download = `${channel}_${starttime.format('YYYYMMDD-HHmmss')}_${endtime.format('YYYYMMDD-HHmmss')}.xml`;
            link.click();

        // エラーがあれば
        }).catch((error) => {

            // エラーをコンソールに表示
            console.error(`Error: ${error}`);
        });

    });

    // 遷移ボタンがクリックされたとき
    $('#urlopen-button').click((event) => {

        // エラーがなければ
        checkAPI().then((api_url) => {

            // 新しいタブで API URL を開く
            window.open(api_url, '_blank');

        // エラーがあれば
        }).catch((error) => {

            // エラーをコンソールに表示
            console.error(`Error: ${error}`);
        });

    });

});
