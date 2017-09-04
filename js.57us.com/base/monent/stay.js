
(function($){
    stay = {
        today:(new Date()),
        diffdaysNum:'',
        defaultset:{
            starMin:0,
            starMax:180,
            endMin:0,
            endMax:180,
            minStep:0,
            chargDay:0,
            chargDay1:0,
            startDom:'#startDate',
            endDom:'#endDate',
            diffDaysDom:''
        },
        init:function(staydata){
			var _this = this;
            staydata = $.extend({},_this.defaultset,staydata);
            staydata.startDom=$(staydata.startDom);
            staydata.endDom=$(staydata.endDom);
            if(staydata.chargDay1<staydata.minStep+staydata.chargDay){
                staydata.chargDay1=staydata.minStep+staydata.chargDay;
            }
            if(staydata.endMin<staydata.minStep+staydata.chargDay){
                staydata.endMin=staydata.minStep+staydata.chargDay
            }
            stay.inputVal(staydata);
            stay.startFun(staydata);
            stay.endFun(staydata);
            stay.compare(staydata.startDom);
            stay.compare(staydata.endDom);
        },
        startFun:function(staydata){
            staydata.startDom.datepicker({
                dateFormat : 'yy-mm-dd',
                dayNamesMin : ['日','一','二','三','四','五','六'],
                monthNames : ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
                altFormat : 'yy-mm-dd',
                yearSuffix:'年',
                showMonthAfterYear:true,
                firstDay : 0,
                showOtherMonths:true,
                minDate:staydata.starMin,
                maxDate:staydata.starMax,
                onSelect:function(dateText,inst){
                    staydata.endDom.datepicker('option', 'minDate', new Date(moment(dateText).add('days',staydata.minStep)));
                    staydata.endDom.datepicker('option', 'maxDate', new Date(moment(dateText).add('days',staydata.minStep+180)));
                    stay.compare(staydata.startDom);
                    if((new Date(staydata.endDom.val()) - new Date(dateText)) <= (staydata.minStep*24*60*60*1000)){
                        staydata.endDom.datepicker("setDate",new Date(moment(dateText).add('days', staydata.minStep)));
                        stay.compare(staydata.endDom);
                    }
                    if(staydata.diffDaysDom){
                        stay.diffdaysNum =stay.diffdays(staydata.startDom.val(),staydata.endDom.val());
                        $(staydata.diffDaysDom).html(stay.diffdaysNum);
                    }

                },
                beforeShow:function(input) {
                    $(input).css({
                        "position": "relative",
                        "z-index": 999
                    });
                }
            });
        },
        endFun:function(staydata){
            staydata.endDom.datepicker('refresh');
            staydata.endDom.datepicker({
                dateFormat : 'yy-mm-dd',
                dayNamesMin : ['日','一','二','三','四','五','六'],
                monthNames : ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
                altFormat : 'yy-mm-dd',
                yearSuffix:'年',
                showMonthAfterYear:true,
                firstDay : 0,
                showOtherMonths:true,
                minDate : staydata.endMin,
                maxDate:staydata.endMax,
                onSelect:function(){
                    stay.compare(staydata.endDom);
                    if(staydata.diffDaysDom){
                    stay.diffdaysNum = stay.diffdays(staydata.startDom.val(),staydata.endDom.val());
                    $(staydata.diffDaysDom).html(stay.diffdaysNum);
                }
            },
            beforeShow:function(input) {
                $(input).css({
                    "position": "relative",
                    "z-index": 999
                });
            }
            });
        },
        transformStr:function(day,strDay){
            switch (day){
                case 1:
                    strDay  = '星期一'; break;
                case 2:
                    strDay  = '星期二'; break;
                case 3:
                    strDay  = '星期三'; break;
                case 4:
                    strDay  = '星期四'; break;
                case 5:
                    strDay  = '星期五'; break;
                case 6:
                    strDay  = '星期六'; break;
                case 0:
                    strDay  = '星期日'; break;
            }
            return strDay;
        },
        compare:function(obj){
            var strDay,
                myDate = new Date(stay.today.getFullYear(),stay.today.getMonth(),stay.today.getDate());
            var day = (obj.datepicker('getDate') - myDate)/(86400000);
            if(!day&&day!=0){return;}
            switch(day){
                case 0:
                strDay = '今天'; break ;
                case 1:
                strDay = '明天'; break;
                case 2:
                strDay = '后天'; break;
                default:
                strDay = stay.transformStr(obj.datepicker('getDate').getDay());
                break;
            }
            obj.datepicker('option', 'appendText',strDay);
        },
        inputVal:function(staydata){
            stay.inputTimes(staydata.startDom,staydata.chargDay);
            stay.inputTimes(staydata.endDom,staydata.chargDay1);
        },
        inputTimes:function(obj,day){
            var m = new Date(moment(stay.today).add('days', day));
            obj.val(m.getFullYear() + "-" + stay.addZero((m.getMonth()+1)) + "-" + stay.addZero(m.getDate()));
        },
        addZero:function(num){
            num < 10 ? num = "0" + num : num ;
            return num;
        },
        diffdays:function(startDate, endDate) {
            if (startDate.indexOf('-') > -1) {
                var st = stay.get_unix_time(startDate);
                var ed = stay.get_unix_time(endDate);
                return (Number(ed) - Number(st)) / (1000 * 60 * 60 * 24) + '天';
            } else {
                return (Number(endDate) - Number(startDate)) / (1000 * 60 * 60 * 24) + '天';
            }
        },
        get_unix_time:function(dateStr) {
            var newstr = dateStr.replace(/-/g, '/');
            var date = new Date(newstr);
            var time_str = date.getTime().toString();
            return time_str;
        }
    }
})(jQuery);
