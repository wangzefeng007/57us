(function ($) {
    //url截取姓名与手机号
    // function GetQueryString(name) {
    //     var reg = new RegExp("(^|_" + name + "=([^&]*)(&|$)");
    //     var result = window.location.search.substr(1).match(reg);
    //     return result ? decodeURIComponent(result[2]) : null;
    // }

    var url = decodeURIComponent(window.location.href);
    var n = url;
    var p = url;

    n = n.substr(n.indexOf("_") + 1);
    var ss = /[^_]*/
    p = p.substr(p.lastIndexOf("_") + 1);


    //inputbox使用
    $('.cbt').inputbox();
    //placeholder使用
    $('input, textarea').placeholder();

    //ajax提交地址前缀
    var host = window.location.protocol + "//" + window.location.hostname;

    //引用姓名与手机号
    var Name = ss.exec(n);
    $('#name').append(Name);
    $('#dname').val(Name);
    var Phone = ss.exec(p);
    $('#phone').append(Phone);
    $('#dphone').val(Phone);
    // console.log(Name,Phone);

    //出发时间与返回时间
    $("#sd").on("focus blur", function () {
        var sd = $(this).val();
        $("#StartDate").text("");
        $('#StartDate').append(sd);
    })
    $("#ed").on("focus blur", function () {
        var ed = $(this).val();
        $("#EndDate").text("");
        $('#EndDate').append(ed);
    })
    //出发城市
    $("#jsContainer").on('mouseleave', function () {
        var StartCity = $("#homecity_name").val();
        $("#StartCity").text("");
        $('#StartCity').append(StartCity);
    })

    $("#homecity_name").on('input change', function () {
        var StartCity = $(this).val();
        $("#StartCity").text("");
        $('#StartCity').append(StartCity);
    })

    //热闹城市注入到城市框
    $("#bj").click(function () {
        $("#homecity_name").val($("#bj").html());
        $("#StartCity").text($("#bj").html());
    })
    $("#ss").click(function () {
        $("#homecity_name").val($("#ss").html());
        $("#StartCity").text($("#ss").html());
    })
    $("#tj").click(function () {
        $("#homecity_name").val($("#tj").html());
        $("#StartCity").text($("#tj").html());
    })
    $("#gz").click(function () {
        $("#homecity_name").val($("#gz").html());
        $("#StartCity").text($("#gz").html());
    })

    //取出可根据实际情况调整出发日期
    $("#adjus").on('click', function () {
        var adjus = $('#adjus input').attr('checked');
        if (adjus == undefined) {
            $("#adjust").val('2');
        } else {
            $("#adjust").val('1');
        }
    })
    //成人计算事件
    var current;
    current = parseInt($("#num").val());
    $("#plus").click(function () {
        if (current >= 1) {
        }
        current = $("#num").val();
        $("#num").val(parseInt(current) + 1);
        current = parseInt($("#num").val());
    })
    $("#minus").click(function () {
        current = $("#num").val();
        if (current > 1) {
            $("#num").val(parseInt(current) - 1);
        } else {
            return false;
        }
    })
    // //点击增加减少事件
    $("#plus").on('click', function () {
        var addult = $("#addul input").val();
        $("#addult").text(addult);
    });
    $("#minus").on('click', function () {
        var addult = $("#addul input").val();
        $("#addult").text(addult);
    });
    //手动输入事件
    $("#addul").on('input change', function () {
        var addult = $("#addul input").val();
        if (!addult.match(/^\d+$/)) {
            layer.msg("请输入正确的数量");
        } else if (addult == 0) {
            layer.msg("出游人数必须大于0");
        }
        var addult = $("#addul input").val();
        $("#addult").text(addult);
    });
    //计算儿童人数
    var kids;
    kids = parseInt($("#num1").val());
    $("#plus1").click(function () {
        if (kids >= 0) {
        }
        kids = $("#num1").val();
        $("#num1").val(parseInt(kids) + 1);
        kids = parseInt($("#num1").val());
    })
    $("#minus1").click(function () {
        kids = $("#num1").val();
        if (kids > 0) {
            $("#num1").val(parseInt(kids) - 1);
        } else {
            return false;
        }
    })
    //点击增加减少事件
    $("#plus1").on('click', function () {
        var minor = $("#mino input").val();
        $("#minor").text(minor);
    });
    $("#minus1").on('click', function () {
        var minor = $("#mino input").val();
        $("#minor").text(minor);
    });
    //手动输入事件
    $("#mino").on('input change', function () {
        var mino = $("#mino input").val();
        if (!mino.match(/^\d+$/)) {
            layer.msg("请输入正确的数量");
        }
        $("#sss").val(mino);
        $("#minor").text(mino);
    });

    //酒店星级选择
    $(".Hotel a").click(function (StarHotel) {
        $(".Hotel a").attr("class", "");
        $(this).attr("class", "on");
        var StarHotel = $(this).text();
        $(".Hotel input").val("");
        $("#StarHotel").text("");
        $('#StarHotel').append(StarHotel);
    });
    $("#zdxj").on("input change", function (e) {
        var StarHotel = $(this).val();
        $("#StarHotel").text("");
        $('#StarHotel').append(StarHotel);
    });
    //游玩城市tabs
    window.onload = function () {
        var $li = $('#tab li');
        var $ul = $('#content ul');

        $li.click(function () {
            var $this = $(this);
            var $t = $this.index();
            $li.removeClass();
            $this.addClass('current');
            $ul.css('display', 'none');
            $ul.eq($t).css('display', 'block');
        })
    }
    //点击载入东西岸及其它城市 east=东 west=西 other=其它
    //东岸城市初始化时加载
    $("#East").empty();
    $("#destination").empty();
    $.ajax({
        //请求方式为get
        type: "get",
        //json文件位置
        url: host + '/Templates/Tour/Customize/city/East.json',
        // data:"纽约",
        //返回数据格式为json
        dataType: "json",
        //请求成功完成后要执行的方法
        success: function (data) {
            // console.log(data);
            var item;
            $.each(data, function (i, city) {
                Description = city.Description.replace(" ", "&nbsp;");
                item = '<li>' +
                    '<a id='+city.AreaID+' data-img='+city.img+' data-cont='+Description+'><i></i>'+city.CnName+'</a>'+
                    '</li>';
                $('#East').append(item);
            });
            //hover事件
            $("#East li").hover(function () {
                var this_a = $(this).find("a");
                var dataimg = this_a.attr('data-img');
                var datacont = this_a.attr('data-cont');
                var box = $(".SceneryBoxMain").html();
                $(this).append(box);
                $('.SceneryBox').find("img").attr('src', dataimg);
                $('.SceneryBox').find("span").text(datacont);
            }, function () {
                $(this).find(".SceneryBox").remove()
            })
            //游玩城市选择
            $("#East li").click(function () {
                var this_a = $(this).find("a");
                var city = this_a.text();
                var id = this_a.attr('id');
                if ($('[data-id="' + id + '"]').length === 0) {
                    html = '<span data-id="' + id + '">' + city + '<i></i></span>';
                    $('.GoResult').append(html);
                    html2 = '<li>' +
                        '<span data-id="' + id + '">' + city + '<i></i></span>' +
                        '<div class="DestBox">' +
                        '<div class="DestBoxT"></div>' +
                        '<div class="DestBoxM">' +
                        '<p class="tit">游玩的城市与景点</p >' +
                        '<table border="0" cellspacing="0" cellpadding="0" width="100%" align="left">' +
                        '<tr>' +
                        '<th width="100" valign="top">' + city + ':</th>' +
                        '<td class="spot">' +
                        '</td>' +
                        '</tr>' +
                        '</table>' +
                        '</div>' +
                        '<div class="DestBoxB"></div>' +
                        '</div>';
                    $("#destination").append(html2);
                    html3 = '<span data-id="' + id + '">' + city + '<i>,</i></span>';
                    $("#EndCity").append(html3);

                }
                $(".GoResult span").click(function () {
                    $(this).remove();
                    var id = $(this).attr("data-id");
                    $("#destination span[data-id=" + id + "]").parent().remove();
                    $("#EndCity span[data-id=" + id + "]").remove();
                });
            })
        }
    });

    $("#west").click(function () {
        $("#West").empty();
        $.ajax({
            //请求方式为get
            type: "get",
            //json文件位置
            url: host + '/Templates/Tour/Customize/city/West.json',
            // data:"纽约",
            //返回数据格式为json
            dataType: "json",
            //请求成功完成后要执行的方法
            success: function (data) {
                // console.log(data);
                var item;
                $.each(data, function (i, city) {
                    Description = city.Description.replace(" ", "&nbsp;");
                    item = '<li>' +
                        '<a id='+city.AreaID+' data-img='+city.img+' data-cont='+Description+'><i></i>'+city.CnName+'</a>'+
                        '</li>';
                    $('#West').append(item);
                });
                //hover事件
                $("#West li").hover(function () {
                    var this_a = $(this).find("a");
                    var dataimg = this_a.attr('data-img');
                    var datacont = this_a.attr('data-cont');
                    var box = $(".SceneryBoxMain").html();
                    $(this).append(box);
                    $('.SceneryBox').find("img").attr('src', dataimg);
                    $('.SceneryBox').find("span").text(datacont);
                }, function () {
                    $(this).find(".SceneryBox").remove()
                });
                //游玩城市选择
                $("#West li").click(function () {
                    var this_a = $(this).find("a");
                    var city = this_a.text();
                    var id = this_a.attr('id');
                    if ($('[data-id="' + id + '"]').length === 0) {
                        html = '<span data-id="' + id + '">' + city + '<em>,</em>' + '<i></i></span>';
                        $('.GoResult').append(html);
                        html2 = '<li>' +
                            '<span data-id="' + id + '">' + city + '<em>,</em><i></i></span>' +
                            '<div class="DestBox">' +
                            '<div class="DestBoxT"></div>' +
                            '<div class="DestBoxM">' +
                            '<p class="tit">游玩的城市与景点</p >' +
                            '<table border="0" cellspacing="0" cellpadding="0" width="100%" align="left">' +
                            '<tr>' +
                            '<th width="100" valign="top">' + city + ':</th>' +
                            '<td class="spot">' +
                            '</td>' +
                            '</tr>' +
                            '</table>' +
                            '</div>' +
                            '<div class="DestBoxB"></div>' +
                            '</div>';
                        $("#destination").append(html2);
                        html3 = '<span data-id="' + id + '">' + city + '<i>,</i></span>';
                        $("#EndCity").append(html3);
                    }
                    $(".GoResult span").click(function () {
                        $(this).remove();
                        var id = $(this).attr("data-id");
                        $("#destination span[data-id=" + id + "]").parent().remove();
                        $("#EndCity span[data-id=" + id + "]").remove();
                    });
                })
            }
        });
    })

    $("#other").click(function () {
        $("#Other").empty();
        $.ajax({
            //请求方式为get
            type: "get",
            //json文件位置
            url: host + '/Templates/Tour/Customize/city/Other.json',
            // data:"纽约",
            //返回数据格式为json
            dataType: "json",
            //请求成功完成后要执行的方法
            success: function (data) {
                // console.log(data);
                var item;
                $.each(data, function (i, city) {
                    Description = city.Description.replace(" ", "&nbsp;");
                    item = '<li>' +
                        '<a id='+city.AreaID+' data-img='+city.img+' data-cont='+Description+'><i></i>'+city.CnName+'</a>'+
                        '</li>';
                    $('#Other').append(item);
                });
                //hover事件
                $("#Other li").hover(function () {
                    var this_a = $(this).find("a");
                    var dataimg = this_a.attr('data-img');
                    var datacont = this_a.attr('data-cont');
                    var box = $(".SceneryBoxMain").html();
                    $(this).append(box);
                    $('.SceneryBox').find("img").attr('src', dataimg);
                    $('.SceneryBox').find("span").text(datacont);
                }, function () {
                    $(this).find(".SceneryBox").remove()
                });
                //游玩城市选择
                $("#Other li").click(function () {
                    var this_a = $(this).find("a");
                    var city = this_a.text();
                    var id = this_a.attr('id');
                    if ($('[data-id="' + id + '"]').length === 0) {
                        html = '<span data-id="' + id + '">' + city + '<em>,</em>' + '<i></i></span>';
                        $('.GoResult').append(html);
                        html2 = '<li>' +
                            '<span data-id="' + id + '">' + city + '<em>,</em><i></i></span>' +
                            '<div class="DestBox">' +
                            '<div class="DestBoxT"></div>' +
                            '<div class="DestBoxM">' +
                            '<p class="tit">游玩的城市与景点</p >' +
                            '<table border="0" cellspacing="0" cellpadding="0" width="100%" align="left">' +
                            '<tr>' +
                            '<th width="100" valign="top">' + city + ':</th>' +
                            '<td class="spot">' +
                            '</td>' +
                            '</tr>' +
                            '</table>' +
                            '</div>' +
                            '<div class="DestBoxB"></div>' +
                            '</div>';
                        $("#destination").append(html2);
                        html3 = '<span data-id="' + id + '">' + city + '<i>,</i></span>';
                        $("#EndCity").append(html3);
                    }
                    $(".GoResult span").click(function () {
                        $(this).remove();
                        var id = $(this).attr("data-id");
                        $("#destination span[data-id=" + id + "]").parent().remove();
                        $("#EndCity span[data-id=" + id + "]").remove();
                    });
                })
            }
        });
    })

    //到达第二页
    $("#next2").click(function () {
        var StartCity = $("#StartCity").text();
        var StartDate = $("#StartDate").text();
        var EndDate = $("#EndDate").text();
        var StarHotel = $("#StarHotel").text();
        var destination = $("#destination span").text();
        var addult = $("#addult").text();
        var minor = $("#minor").text();
        if (StartCity == "") {
            layer.msg("请选择出发城市");
            return
        } else if (StartDate == "") {
            layer.msg("请选择出发时间");
            return
        } else if (EndDate == "") {
            layer.msg("请选择返回时间");
            return
        }else if(!addult.match(/^\d+$/) || addult == "0"){
            layer.msg("请正确输入出游成人数量");
            return
        }else if(addult == "0") {
            layer.msg("成人出游人数不能为0");
            return
        }else if (!minor.match(/^\d+$/) || !minor.match(/^(0|[1-9]\d{0,9})$/)) {
            layer.msg("请正确输入出游儿童数量");
            return
        } else if (StarHotel == "") {
            layer.msg("请选择入住酒店");
            return
        } else if (destination == "") {
            layer.msg("请选择游玩城市")
            return
        }
        $("#index_1").css('display', 'none');
        $("#index_2").css('display', 'block');
        $(".StepProcess").removeClass("Step1");
        $(".StepProcess").addClass("Step2");
        $('body,html').animate({ scrollTop: 147 }, 300);
        // 请求到城市景点并显示出来
        $('#ScenicSpots').empty();
        // $('.Attractions').empty();
        $("#EndCity span").each(function () {
            var encity = $(this).attr("data-id");
            var zhcity = $(this).text();
            // console.log(zhcity);
            $.ajax({
                //请求方式为get
                type: "get",
                //json文件位置
                // url:'http://tour.57us.com/ajaxtour.html',
                url: host + '/Templates/Tour/Customize/data/' + encity + '.json',
                // data:{},
                //返回数据格式为json
                dataType: "json",
                //请求成功完成后要执行的方法
                success: function (data) {
                    // console.log(data);
                    str = zhcity.replace(/[,'"]/g, "");
                    html = '<tr>' +
                        '<th width="110">想玩的景点：</th>' +
                        '<td>' +
                        '<div class="AttractionsBox cf">' +
                        '<p>' + str + '</p>' +
                        '<ul class="Attractions mt10 Attr">' +
                        '</ul>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                    $('#ScenicSpots').append(html);
                    var item;
                    $.each(data, function (i, scenic) {
                        // console.log(data.city);
                        Description = scenic.Description.replace(" ", "&nbsp;");
                        item = '<li>' +
                            '<a id='+encity+' data-img='+scenic.img+' data-cont='+Description+'><i></i>' + scenic.AttractionsName+ '</a>'+
                            '</li>';
                        $('.Attr:last').append(item);
                    });
                    $(".Attr:last li a").hover(function () {
                        this_a = $(this);
                        var dataimg = this_a.attr('data-img');
                        var datacont = this_a.attr('data-cont');
                        var box = $(".SceneryBoxMain").html();
                        this_a.parent("li").append(box);
                        $('.SceneryBox').find("img").attr('src', dataimg);
                        $('.SceneryBox').find("span").text(datacont);
                    }, function () {
                        this_a.parent("li").find(".SceneryBox").remove()
                    });

                    $(".Attr:last a").click(function () {
                        if ($(this).is('.on')) {
                            $(this).removeClass("on");
                            var id = $(this).text();
                            $("#Scenic span[data-id=" + id + "]").remove();
                            var lid = $(this).attr("id");
                            $("#destination span").each(function () {
                                var rid = $(this).attr("data-id");
                                if (rid == lid) {
                                    $("#destination b[id=" + id + "]").remove();
                                    var ge = $(this).next().find('b').length;
                                    $(this).find('i').html("");
                                    $(this).find('i').append('(' + ge + ')');
                                }
                            });
                        } else {
                            $(this).addClass("on");
                            var city = $(this).text();
                            var id = $(this).text();
                            if ($('[data-id="' + id + '"]').length === 0) {
                                html = '<span data-id="' + id + '">' + city + ',' + '</span>';
                                $("#Scenic").append(html);
                            }
                            var lid = $(this).attr("id");
                            $("#destination span").each(function () {
                                var rid = $(this).attr("data-id");
                                if (rid == lid) {
                                    if ($('[id="' + id + '"]').length === 0) {
                                        html = '<b id="' + id + '">' + id + '</b>';
                                        $(this).next().find('td', 'spot').append(html);
                                        var ge = $(this).next().find('b').length;
                                        $(this).find('i').html("");
                                        $(this).find('i').append('(' + ge + ')');
                                    }
                                }
                            });
                        }
                    })
                    //旅游景点重新增加class
                    $(".spot b").each(function () {
                        var jdclass = $(this).text();
                        // console.log(jdclass);
                        $(".Attr a").each(function () {
                            var jd = $(this).text();
                            if (jdclass == jd) {
                                $(this).addClass("on");
                            }
                        })
                    });
                }
            });
        });
    });
    //返回第一页面
    $("#prev1").click(function () {
        $("#index_1").css('display', 'block');
        $("#index_2").css('display', 'none');
        $(".StepProcess").removeClass("Step2");
        $(".StepProcess").addClass("Step1");
        $('body,html').animate({ scrollTop: 0 }, 400);
    });
    //到达第三页面
    $("#next3").click(function () {
        $("#index_2").css('display', 'none');
        $("#index_3").css('display', 'block');
        $(".StepProcess").removeClass("Step2");
        $(".StepProcess").addClass("Step3");
        $('body,html').animate({ scrollTop: 147 }, 300);
        //需求选择
        $("#Customiza").empty();
        $.ajax({
            //请求方式为get
            type: "get",
            //json文件位置
            url: host + '/Templates/Tour/Customize/custom/custom.json',
            // data:"纽约",
            //返回数据格式为json
            dataType: "json",
            //请求成功完成后要执行的方法
            success: function (data) {
                // console.log(data);
                var custom;
                $.each(data, function (i, custom) {
                    Description = custom.Description.replace(" ", "&nbsp;");
                    custom = '<li>' +
                        '<a id= ' + custom.EnName + ' data-img=' + custom.img + ' data-cont= ' + Description + '><i></i>' + custom.CustomName + '</a>' +
                        '</li>';
                    $('#Customiza').append(custom);
                });
                //hover事件
                $("#Customiza li").hover(function () {
                    var this_a = $(this).find("a");
                    var dataimg = this_a.attr('data-img');
                    var datacont = this_a.attr('data-cont');
                    var box = $(".SceneryBoxMain").html();
                    $(this).append(box);
                    $('.SceneryBox').find("img").attr('src', dataimg);
                    $('.SceneryBox').find("span").text(datacont);
                }, function () {
                    $(this).find(".SceneryBox").remove()
                })
                //定制选择
                $("#Customiza li a").click(function () {
                    if($(this).is(".on")){
                        $(this).removeClass("on");
                        var id = $(this).attr('id');
                        $("#Customizatin b[data-id=" + id + "]").remove();
                        $("#Customizatinfrom b[data-id=" + id + "]").remove();
                    }else {
                        $(this).addClass("on");
                        var custom = $(this).text();
                        var id = $(this).attr('id');
                        if ($('[data-id="' + id + '"]').length === 0) {
                            html = '<b data-id="' + id + '">' + custom + '</b>';
                            $("#Customizatin").append(html);
                            html2 = '<b data-id="' + id + '">' + custom + ',</b>';
                            $("#Customizatinfrom").append(html2);
                        }
                    }
                });
                //增加class
                $("#Customizatin b").each(function () {
                    var dzclass = $(this).text();
                    $("#Customiza a").each(function () {
                        var dz = $(this).text();
                        if (dzclass == dz) {
                            $(this).addClass("on");
                        }
                    })
                });
            }
        });

        //需求选择
        $(".Demand").empty();
        $.ajax({
            //请求方式为get
            type: "get",
            //json文件位置
            url: host + '/Templates/Tour/Customize/Demand/Demand.json',
            //返回数据格式为json
            dataType: "json",
            //请求成功完成后要执行的方法
            success: function (data) {
                // console.log(data);
                var demand;
                $.each(data, function (i, demand) {

                    demand = '<a id= ' + demand.EnName + ' data-img=' + demand.img + ' data-cont= ' + demand.Description + '><i></i>' + demand.DemandName + '</a>';
                    $('.Demand').append(demand);
                });
                //需求选择
                $(".Demand a").click(function () {
                    if($(this).is(".on")){
                        $(this).removeClass("on");
                        var id = $(this).attr('id');
                        $("#Demand b[data-id=" + id + "]").remove();
                        $("#Demandfrom b[data-id=" + id + "]").remove();
                    }else {
                        $(this).addClass("on");
                        var Demand = $(this).text();
                        var id = $(this).attr('id');
                        if ($('[data-id="' + id + '"]').length === 0) {
                            html = '<b data-id="' + id + '">' + Demand + '</b>';
                            $("#Demand").append(html);
                            html2 = '<b data-id="' + id + '">' + Demand + ',</b>';
                            $("#Demandfrom").append(html2);
                        }
                    }
                });
                // //增加class
                $("#Demand b").each(function () {
                    var dzclass = $(this).text();
                    $(".Demand a").each(function () {
                        var dz = $(this).text();
                        if (dzclass == dz) {
                            $(this).addClass("on");
                        }
                    })
                });
            }
        });

        //其它需求
        $(".OtherDemand").empty();
        $.ajax({
            //请求方式为get
            type: "get",
            //json文件位置
            url: host + '/Templates/Tour/Customize/OtherDemand/OtherDemand.json',
            //返回数据格式为json
            dataType: "json",
            //请求成功完成后要执行的方法
            success: function (data) {
                // console.log(data);
                var other;
                $.each(data, function (i, other) {
                    other = '<a id= ' + other.EnName + ' data-img=' + other.img + ' data-cont= ' + other.Description + '><i></i>' + other.OtherDemandName + '</a>';
                    $('.OtherDemand').append(other);
                });
                //需求选择
                $(".OtherDemand a").click(function () {
                    if($(this).is(".on")){
                        $(this).removeClass("on");
                        var id = $(this).attr('id');
                        $("#OtherDemand b[data-id=" + id + "]").remove();
                        $("#OtherDemandfrom b[data-id=" + id + "]").remove();
                    }else {
                        $(this).addClass("on");
                        var OtherDemand = $(this).text();
                        var id = $(this).attr('id');
                        if ($('[data-id="' + id + '"]').length === 0) {
                            html = '<b data-id="' + id + '">' + OtherDemand + '</b>';
                            $("#OtherDemand").append(html);
                            html2 = '<b data-id="' + id + '">' + OtherDemand + ',</b>';
                            $("#OtherDemandfrom").append(html2);
                        }
                    }
                });
                //增加class
                $("#OtherDemand b").each(function () {
                    var dzclass = $(this).text();
                    $(".OtherDemand a").each(function () {
                        var dz = $(this).text();
                        if (dzclass == dz) {
                            $(this).addClass("on");
                        }
                    })
                });
            }
        });
    });

    //返回第二页面
    $("#prev2").click(function () {
        $("#index_2").css('display', 'block');
        $("#index_3").css('display', 'none');
        $(".StepProcess").removeClass("Step3");
        $(".StepProcess").addClass("Step2");
        $('body,html').animate({ scrollTop: 147 }, 300);
    });

    //到达第四页面
    $("#next4").click(function () {
        $("#index_3").css('display', 'none');
        $("#index_4").css('display', 'block');
        $(".StepProcess").removeClass("Step3");
        $(".StepProcess").addClass("Step4");
        $('body,html').animate({ scrollTop: 147 }, 300);
        $("#dname").on("input change", function (e) {
            var dname = $(this).val();
            $("#name").text("");
            $('#name').append(dname);
        });
        $("#dphone").on("input change", function (e) {
            var dphone = $(this).val();
            $("#phone").text("");
            $('#phone').append(dphone);
        });
        $("#dmail").on("input change", function (e) {
            var dmail = $(this).val();
            $("#mail").text("");
            $('#mail').append(dmail);
        });
        //获取图形验证码值
        $("#yzm").on('input change', function () {
            var yzm = $(this).val();
            $("#Yzm").val();
            $("#Yzm").val(yzm);
        });
        //获取短信验证码值
        $("#sms").on('input change', function () {
            var sms = $(this).val();
            $("#Sms").val();
            $("#Sms").val(sms);
        });
        //获取短信验证点击倒计时
        var wait = 60;
        get_code_time = function (o) {
            if (wait == 0) {
                o.removeAttribute("disabled");
                o.value = "获取动态密码";
                wait = 60;
            } else {
                o.setAttribute("disabled", true);
                o.value = "(" + wait + ")秒后重新获取";
                wait--;
                setTimeout(function () {
                    get_code_time(o)
                }, 1000)
            }
        };
        //获取短信验证码
        $("#SmsBtn").click(function () {
            var o = this;
            var Phone = $("#phone").text();
            var Yzm = $("#Yzm").val();
            if (!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(Phone)) {
                layer.msg("请输入正确手机号码");
                return
            } else if (Yzm == "") {
                layer.msg("验证码不能为空")
                return
            }
            $.ajax({
                //请求方式为get
                type: "post",
                //json文件位置
                url: host + '/ajaxtour.html',
                data: {
                    'Intention': 'PhoneCode',
                    'User': Phone,
                    'ImageCode': Yzm,
                },
                //返回数据格式为json
                dataType: "json",
                error: function () {
                    layer.msg("网络出错，请稍后再试！");
                },
                //请求成功完成后要执行的方法\
                success: function (data) {
                    //200=成功 100=异常 101=图形验证码错误
                    if (data.ResultCode == "200") {
                        get_code_time(o);
                        layer.msg("短信验证码发送成功");
                    } else {
                        layer.msg(data.Message);
                        return
                    }
                }
            });
        })
        // 填写姓名，手机号，邮箱后提到数据到后面
        $("#submit").click(function () {
            var Name = $("#name").text();
            var Phone = $("#phone").text();
            var StartCity = $("#StartCity").text();
            var StartDate = $('#StartDate').text();
            var EndDate = $("#EndDate").text();
            var adjust = $("#adjust").val();
            var addult = $("#addult").text();
            var minor = $("#minor").text();
            var StarHotel = $("#StarHotel").text();
            var EndCity = $("#EndCity").text();
            var Scenic = $("#Scenic").text();
            var Customizatin = $("#Customizatinfrom").text();
            var Demand = $("#Demandfrom").text();
            var OtherDemand = $("#OtherDemandfrom").text();
            var Mail = $("#mail").text();
            var Sms = $("#Sms").val();
            // console.log(Sms);
            if (Name == "") {
                layer.msg("请输入姓名");
                return
            } else if (!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(Phone)) {
                layer.msg("请输入正确手机号码");
                return
            } else if (Sms == "") {
                layer.msg("短信验证不能为空");
                return
            } else if(!/^\d{6}$/i.test(Sms)) {
                layer.msg("请输入6位纯数字短信验证码");
                return
            } else if (!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(Mail)) {
                layer.msg("邮箱格式不正确");
                return
            }
            $.ajax({
                //请求方式为get
                type: "post",
                //json文件位置
                url: host + '/ajaxcustom.html',
                data: {
                    'Intention': 'CustomApi',
                    'Name': Name,
                    'Phone': Phone,
                    'StartCity': StartCity,
                    'StartDate': StartDate,
                    'EndDate': EndDate,
                    'Adjust': adjust,
                    'Addult': addult,
                    'Minor': minor,
                    'StarHotel': StarHotel,
                    'EndCity': EndCity,
                    'ScenicSpots': Scenic,
                    'Customizatin': Customizatin,
                    'Demand': Demand,
                    'OtherDemand': OtherDemand,
                    'Mail': Mail,
                    'Code': Sms,
                },
                //返回数据格式为json
                dataType: "json",
                error: function () {
                    layer.msg("网络出错，请稍后再试！");
                },
                //请求成功完成后要执行的方法
                success: function (data) {
                    //200=成功 102=短信验证码错误 103=短信验证码过期
                    if (data.ResultCode == 200) {
                        var orderurl = data.Url;
                        layer.open({
                            type: 1,
                            skin: 'DemoSureBox', //样式类名
                            closeBtn: 1, //不显示关闭按钮
                            shift: 2,
                            title: $('.SureBoxTit').text(),
                            area: ['502px', '323px'],
                            shadeClose: true, //开启遮罩关闭
                            content: $(".SureBoxMain").html(),
                            success: function () {
                                $('.CheckBtn').attr('href', orderurl);
                            }
                        });
                        $("#submit").attr("disabled", true);
                        $("#submit").removeClass("next");
                        $("#submit").addClass("disabled");
                    }
                    if (data.ResultCode == "102") {
                        layer.msg("短信验证码错误！");
                        return
                    } else if (data.ResultCode == "103") {
                        layer.msg("短信验证码过期！");
                        return
                    }
                }
            });
        })
    });
    //返回第三页面
    $("#prev3").click(function () {
        $("#index_3").css('display', 'block');
        $("#index_4").css('display', 'none');
        $(".StepProcess").removeClass("Step4");
        $(".StepProcess").addClass("Step3");
        $('body,html').animate({ scrollTop: 147 }, 300);
    });
})(jQuery);
