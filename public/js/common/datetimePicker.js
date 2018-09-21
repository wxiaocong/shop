//format doc address : http://momentjs.com/docs/#/displaying/format/
// choose date format like:1986-05-05
$('.datepicker').datetimepicker({
    format: 'YYYY-MM-DD'
});

// choose time format like:21:30
$('.timepicker').datetimepicker({
    format: 'HH:mm'
});

// choose date & time format like:1986-05-05 21:30:30
$('.datetimepicker').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss'
});

// choose date & time(accurate to minute) format like:1986-05-05 21:30
$('.dateminutepicker').datetimepicker({
    format: 'YYYY-MM-DD HH:mm'
});

// choose min current date format like:当天时间
$('.minDatepicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: new Date()
});

// choose year month format like:1986-05
$('.yearMonthPicker').datetimepicker({
    format: 'YYYY-MM'
});

//taday
$(".search-form").find(".today").click(function() {
    $(".search-form").find(".startDate").val($(this).attr("data"));
    $(".search-form").find(".endDate").val($(this).attr("data"));
});

// 7 days ago
$(".search-form").find(".week").click(function() {
    $(".search-form").find(".startDate").val($(this).attr("data"));
    $(".search-form").find(".endDate").val($(".search-form").find(".today").attr("data"));
});

// 30 days ago
$(".search-form").find(".month").click(function() {
    $(".search-form").find(".startDate").val($(this).attr("data"));
    $(".search-form").find(".endDate").val($(".search-form").find(".today").attr("data"));
});