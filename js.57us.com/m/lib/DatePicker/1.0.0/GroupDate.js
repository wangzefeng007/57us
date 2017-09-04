var obj = {
    date: new Date(),
    year: -1,
    month: -1,
    priceArr: [],
    monthArr:[],
    monthArr2:[],
};
var htmlObj = {
    header: "",
    left: "",
    right: ""
};
var elemId = null;

function getAbsoluteLeft(objectId) {
    var o = document.getElementById(objectId)
    var oLeft = o.offsetLeft;
    while (o.offsetParent != null) {
        oParent = o.offsetParent
        oLeft += oParent.offsetLeft
        o = oParent
    }
    return oLeft
}
//获取控件上绝对位置
function getAbsoluteTop(objectId) {
    var o = document.getElementById(objectId);
    var oTop = o.offsetTop + o.offsetHeight + 200;
    while (o.offsetParent != null) {
        oParent = o.offsetParent
        oTop += oParent.offsetTop
        o = oParent
    }
    return oTop
}
//获取控件宽度

function getElementWidth(objectId) {
    x = document.getElementById(objectId);
    return x.clientHeight;
}
var pickerEvent = {
    Init: function(elemid) {
        if (obj.year == -1) {
            dateUtil.getCurrent();
        }
        for (var item in pickerHtml) {
            pickerHtml[item]();
        }
        var p = document.getElementById("calendar_choose");
        if (p != null) {
            $("#calendar_tab").empty();
            var html = htmlObj.right;
            elemId = elemid;
            var elemObj = document.getElementById(elemid);
            $("#calendar_tab").append(html);

        }else {
            var html = '<div id="calendar_choose" class="diyDatePick" style="display: block; position: absolute;background-color: #eeeeee;height: 100%"><header class="bar bar-nav diybar" style="height: 2.5rem"><a class="back pull-left"><i class="icon iconfont">&#xe604;</i></a><h1 class="title">选择出行日期</h1></header>'
            html += htmlObj.left;
            html += htmlObj.header;
            html += '<div class="basefix" id="bigCalendar" style="display: block;">';
            html += htmlObj.right;
            html += '<div style="clear: both;"></div>';
            html += "</div>";
            html += "</div>";
            elemId = elemid;
            var elemObj = document.getElementById(elemid);
            $("#pageDate").append(html);
            $("#month .swiper-slide").eq(0).addClass('on');
            var swiper = new Swiper('.swiper-container', {
                slidesPerView: 4
            });
            // $("#calendar_choose .back").on('click',function () {
            //     pickerEvent.remove();
            // })
        }

        document.getElementById("calendar_choose").style.zIndex = 1000;
        var tds = document.getElementById("calendar_tab").getElementsByTagName("td");
        for (var i = 0; i < tds.length; i++) {
            if (tds[i].getAttribute("date") != null && tds[i].getAttribute("date") != "" && tds[i].getAttribute("price") != "-1") {
                tds[i].onclick = function() {
                    commonUtil.chooseClick(this)
                };
            }
        }
        // return html;
        //return elemObj;
    },
    //下一个月方法
    getNext: function(_this) {
        var month = _this.attr('data-id');
        dateUtil.getNexDate(month);
        pickerEvent.Init(elemId);
        $('.swiper-slide').removeClass('on');
        _this.addClass('on');
    },
    //回到今天点击
    getToday: function() {
        dateUtil.getCurrent();
        pickerEvent.Init(elemId);
    },
    //获取ajax的日期与价格
    setPriceArr: function(arr) {
        obj.priceArr = arr.Date;
        var month = arr.MonthArr2;
        obj.year = month[0].split('-')[0];
        obj.month = month[0].split('-')[1];
    },
    //获取月份
    setMonthArr: function (arr) {
        obj.monthArr = arr.MonthArr;
        obj.monthArr2 = arr.MonthArr2;
    },
    // remove: function() {
    //     var p = document.getElementById("calendar_choose");
    //     if (p != null) {
    //         document.body.removeChild(p);
    //     }
    // },
    isShow: function() {
        var p = document.getElementById("calendar_choose");
        if (p != null) {
            return true;
        } else {
            return false;
        }
    }
}
var pickerHtml = {
    getHead: function() {
        var head = '<div class="diyCont">' +
            '<div class="weekDays bar-tab">' +
            '<span class="tab-item">日</span>' +
            '<span class="tab-item">一</span>' +
            '<span class="tab-item">二</span>' +
            '<span class="tab-item">三</span>' +
            '<span class="tab-item">四</span>' +
            '<span class="tab-item">五</span>' +
            '<span class="tab-item">六</span>' +
            '</div>' +
            '</div>';
        htmlObj.header = head;
    },
    getLeft: function() {
        var html = '';
        for(var i=0; i<obj.monthArr.length; i++){
            html+= '<div class="swiper-slide" onclick="pickerEvent.getNext($(this))" data-id="'+obj.monthArr2[i]+'">'+obj.monthArr[i]+'</div>'
        }
        var left = '<div class="diyMonth swiper-container">' +
            '<div class="swiper-wrapper" id="month">'+html+
            '</div>' +
            '</div>';
        htmlObj.left = left;
    },
    getRight: function() {
        var days = dateUtil.getLastDay();
        var week = dateUtil.getWeek();
        var html = '<div class="dateCont"><table id="calendar_tab" border="0" cellspacing="0" cellpadding="0" width="100%"><tbody>';
        var index = 0;
        for (var i = 1; i <= 42; i++) {
            if (index == 0) {
                html += "<tr>";
            }
            var c = week > 0 ? week : 0;
            if ((i - 1) >= week && (i - c) <= days) {
                var price = commonUtil.getPrice((i - c));
                var priceStr = "";
                var classStyle = "";
                if (price != -1) {
                    priceStr = "<dfn>&yen;&nbsp;</dfn>" + price;
                    classStyle = "class='has'";
                }
                if (price != -1 && obj.year == new Date().getFullYear() && obj.month == new Date().getMonth() + 1 && i - c == new Date().getDate()) {
                    classStyle = "class='has choseDay today'";
                }
                //判断今天
                if (obj.year == new Date().getFullYear() && obj.month == new Date().getMonth() + 1 && i - c == new Date().getDate()) {
                    html += '<td  ' + classStyle + ' date="' + obj.year + "-" + obj.month + "-" + (i - c) + '" price="' + price + '">' +
                        '<div class="day">今天</div>' +
                        '<p class="price">' + priceStr + '</p>' +
                        '</td>';
                } else {
                    html += '<td  ' + classStyle + ' date="' + obj.year + "-" + obj.month + "-" + (i - c) + '" price="' + price + '">' +
                        '<div class="day">' + (i - c) + '</div>' +
                        '<p class="price">' + priceStr + '</p>' +
                        '</td>';
                }
                if (index == 6) {
                    html += '</tr>';
                    index = -1;
                }
            } else {
                html += "<td></td>";
                if (index == 6) {
                    html += "</tr>";
                    index = -1;
                }
            }
            index++;
        }
        html += "</tbody></table></div>";
        htmlObj.right = html;
    }
}
var dateUtil = {
    //根据日期得到星期
    getWeek: function() {
        var d = new Date(obj.year, obj.month - 1, 1);
        return d.getDay();
    },
    //得到一个月的天数
    getLastDay: function() {
        var new_year = obj.year; //取当前的年份
        var new_month = obj.month; //取下一个月的第一天，方便计算（最后一不固定）
        var new_date = new Date(new_year, new_month, 1); //取当年当月中的第一天
        return (new Date(new_date.getTime() - 1000 * 60 * 60 * 24)).getDate(); //获取当月最后一天日期
    },
    //回到今天方法
    getCurrent: function() {
        var dt = obj.date;
        obj.year = dt.getFullYear();
        obj.month = dt.getMonth() + 1;
        obj.day = dt.getDate();
    },
    //点击月份，获取当前月分
    getNexDate: function(month) {
        obj.year = month.split('-')[0];
        obj.month = month.split('-')[1];
    }
}
var commonUtil = {
    getPrice: function(day) {
        var dt = obj.year + "-";
        if (obj.month < 10) {
            dt += "" + obj.month;
        } else {
            dt += obj.month;
        }
        if (day < 10) {
            dt += "-" + "0" + day;
        } else {
            dt += "-" + day;
        }

        for (var i = 0; i < obj.priceArr.length; i++) {
            if (obj.priceArr[i].Date == dt) {
                return obj.priceArr[i].Price.split('.')[0];
            }
        }
        return -1;
    },
    chooseClick: function(sender) {
        var date = sender.getAttribute("date");
        var price = sender.getAttribute("price");
        // var el = document.getElementById(elemId);
        $(".dateChose").text(date);
        // pickerEvent.remove();
        ajaxLoad(date);
        history.go(-1);

    }
}

$(document).bind("click", function(event) {
    var e = event || window.event;
    var elem = e.srcElement || e.target;
    while (elem) {
        if (elem.id == "calendar_choose") {
            return;
        }
        elem = elem.parentNode;
    }
    // pickerEvent.remove();
});