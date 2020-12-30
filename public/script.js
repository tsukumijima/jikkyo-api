
$(function() {

    // 現在時刻
    const date = moment().set('second', 0);

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

    // ダウンロードボタンがクリックされたとき
    $('#download-button').click((event) => {
        
        // url 構築
        const url = `/api/kakolog/${channel}?starttime=${starttime.unix()}&endtime=${endtime.unix()}&format=xml`;

        // 仮想の a 要素を作成してダウンロードさせる
        const link = document.createElement('a');
        link.href = url;
        link.download = `${channel}_${starttime.format('YYYYMMDD-HHmmss')}_${endtime.format('YYYYMMDD-HHmmss')}.xml`;
        link.click();

    });

    // 遷移ボタンがクリックされたとき
    $('#urlopen-button').click((event) => {
        
        // url 構築
        const url = `/api/kakolog/${channel}?starttime=${starttime.unix()}&endtime=${endtime.unix()}&format=xml`;

        // 新しいタブで API URL を開く
        window.open(url, '_blank')

    });

});
