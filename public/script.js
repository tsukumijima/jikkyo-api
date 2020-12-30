
$(function() {

    // 現在自己億
    const date = moment().set('second', 0);

    // 取得開始時刻の日付ピッカー
    $('#datepicker-start').datetimepicker({
        dayViewHeaderFormat: 'YYYY年MM月',
        format: 'YYYY/MM/DD',
        locale: 'ja',
        maxDate: date,
        defaultDate: date,
    });
    
    // 取得開始時刻の時刻ピッカー
    $('#timepicker-start').datetimepicker({
        dayViewHeaderFormat: 'HH:mm:ss',
        format: 'HH:mm:ss',
        locale: 'ja',
        maxDate: date,
        defaultDate: date,
    });

    // 取得終了時刻の日付ピッカー
    $('#datepicker-end').datetimepicker({
        dayViewHeaderFormat: 'YYYY年MM月',
        format: 'YYYY/MM/DD',
        locale: 'ja',
        maxDate: date,
        defaultDate: date,
    });
    
    // 取得終了時刻の時刻ピッカー
    $('#timepicker-end').datetimepicker({
        dayViewHeaderFormat: 'HH:mm:ss',
        format: 'HH:mm:ss',
        locale: 'ja',
        maxDate: date,
        defaultDate: date,
    });

    

});
