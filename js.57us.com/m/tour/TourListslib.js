/**
 * Created by Foliage on 2016/10/28.
 */

//获取当前时间
function CurentTime() {
    var now = new Date();
    var year = now.getFullYear(); //年
    var month = now.getMonth() + 1; //月
    var day = now.getDate(); //日
    var clock = year + "-";
    if(month < 10)
        clock += "0";
    clock += month + "-";
    if(day < 10)
        clock += "0";
    clock += day + " ";
    return(clock);
}

//增加月份 格式：yyyy-mm
function gettMonth(date, n) {
    var arr = date.split('-');
    var year = arr[0]; //获取当前日期的年份
    var month = arr[1]; //获取当前日期的月份
    var day = arr[2]; //获取当前日期的日
    var days = new Date(year, month, 0);
    days = days.getDate(); //获取当前日期中的月的天数
    var year2 = year;
    var month2 = parseInt(month) + n;
    if(month2 == 13) {
        year2 = parseInt(year2) + 1;
        month2 = 1;
    } else if(month2 == 14) {
        year2 = parseInt(year2) + 1;
        month2 = 2;
    } else if(month2 == 15) {
        year2 = parseInt(year2) + 1;
        month2 = 3;
    } else if(month2 == 16) {
        year2 = parseInt(year2) + 1;
        month2 = 4;
    } else if(month2 == 17) {
        year2 = parseInt(year2) + 1;
        month2 = 5;
    } else if(month2 == 18) {
        year2 = parseInt(year2) + 1;
        month2 = 6;
    } else if(month2 == 19) {
        year2 = parseInt(year2) + 1;
        month2 = 7;
    } else if(month2 == 20) {
        year2 = parseInt(year2) + 1;
        month2 = 8;
    } else if(month2 == 21) {
        year2 = parseInt(year2) + 1;
        month2 = 9;
    } else if(month2 == 22) {
        year2 = parseInt(year2) + 1;
        month2 = 10;
    } else if(month2 == 23) {
        year2 = parseInt(year2) + 1;
        month2 = 11;
    } else if(month2 == 24) {
        year2 = parseInt(year2) + 1;
        month2 = 12;
    }
    var day2 = day;
    var days2 = new Date(year2, month2, 0);
    days2 = days2.getDate();
    if(day2 > days2) {
        day2 = days2;
    }
    if(month2 < 10) {
        month2 = '0' + month2;
    }
    var t2 = year2 + '' + month2;
    return t2;
}
//增加月份 格式：yyyy年mm月
function gettMonthb(date, n) {
    var arr = date.split('-');
    var year = arr[0].substring(arr[0].length-2); //获取当前日期的年份
    var month = arr[1]; //获取当前日期的月份
    var day = arr[2]; //获取当前日期的日
    var days = new Date(year, month, 0);
    days = days.getDate(); //获取当前日期中的月的天数
    var year2 = year;
    var month2 = parseInt(month) + n;
    if(month2 == 13) {
        year2 = parseInt(year2) + 1;
        month2 = 1;
    } else if(month2 == 14) {
        year2 = parseInt(year2) + 1;
        month2 = 2;
    } else if(month2 == 15) {
        year2 = parseInt(year2) + 1;
        month2 = 3;
    } else if(month2 == 16) {
        year2 = parseInt(year2) + 1;
        month2 = 4;
    } else if(month2 == 17) {
        year2 = parseInt(year2) + 1;
        month2 = 5;
    } else if(month2 == 18) {
        year2 = parseInt(year2) + 1;
        month2 = 6;
    } else if(month2 == 19) {
        year2 = parseInt(year2) + 1;
        month2 = 7;
    } else if(month2 == 20) {
        year2 = parseInt(year2) + 1;
        month2 = 8;
    } else if(month2 == 21) {
        year2 = parseInt(year2) + 1;
        month2 = 9;
    } else if(month2 == 22) {
        year2 = parseInt(year2) + 1;
        month2 = 10;
    } else if(month2 == 23) {
        year2 = parseInt(year2) + 1;
        month2 = 11;
    } else if(month2 == 24) {
        year2 = parseInt(year2) + 1;
        month2 = 12;
    }
    var day2 = day;
    var days2 = new Date(year2, month2, 0);
    days2 = days2.getDate();
    if(day2 > days2) {
        day2 = days2;
    }
    if(month2 < 10) {
        month2 = '0' + month2;
    }
    var t2 = year2 + '年' + month2 + '月';
    return t2;
}
    
function StartDate() {
    html = '<span data-value="All" class="All">不限</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 0)+'">'+gettMonthb(CurentTime(), 0)+'</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 1)+'">'+gettMonthb(CurentTime(), 1)+'</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 2)+'">'+gettMonthb(CurentTime(), 2)+'</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 3)+'">'+gettMonthb(CurentTime(), 3)+'</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 4)+'">'+gettMonthb(CurentTime(), 4)+'</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 5)+'">'+gettMonthb(CurentTime(), 5)+'</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 6)+'">'+gettMonthb(CurentTime(), 6)+'</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 7)+'">'+gettMonthb(CurentTime(), 7)+'</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 8)+'">'+gettMonthb(CurentTime(), 8)+'</span>'+
        '<span data-value="'+gettMonth(CurentTime(), 9)+'">'+gettMonthb(CurentTime(), 9)+'</span>';
    return html;
}