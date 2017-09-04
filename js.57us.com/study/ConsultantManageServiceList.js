/**
 * Created by Foliage on 2016/9/30.
 */
$(function () {
    //服务列表初始化加载
    Ajax();
    //点击搜素时
    $("#ServiceSearchBtn").on('click',function () {
        Ajax();
    })
    $(".CenterCaseMenu li").click(function () {
        $(this).addClass("on").siblings().removeClass("on");
        $("#CourseSearch").val('');
        $("#CourseSearch").attr('value','');
        Ajax();
    })
})

function Ajax(Page) {
    var AjaxData = {
        'Intention': 'ConsultantServiceList',
        'Page':Page,
        'Status':$(".CenterCaseMenu .on").attr('data-type'),
        'Keyword':$("#ServiceSearch").val(),
    }
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/consultantmanageajax/",  //提交地址
        data: AjaxData,
        beforeSend: function () { //加载过程效果
            $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                DataSuccess(data,AjaxData)
            }else if(data.ResultCode == "100"){
                layer.msg('加载出错，请刷新页面重新选择!');
            }else if(data.ResultCode == "101"){
                DataFailure(AjaxData,0);
            }else if(data.ResultCode == '102'){
                DataSuccess(data,AjaxData)
            }else if(data.ResultCode == '103'){
                DataFailure(AjaxData,1);
            }
        },
        complete: function () { //加载完成提示
            $("#loading").hide();
        }
    });
}

//Ajax请求成功，并有数据
function DataSuccess(data,AjaxData) {
    $("#ServiceList").empty();
    $("#NoServiceBox").hide();
    $("#AddCase").show();
    $("#List").show();
    if(AjaxData.Status == '3'){
        var item;
        $.each(data.Data, function(i, list) {
            item = '<li>' +
                '<div class="BoxR">' +
                '<p class="price">￥<em>'+list.ServiceList_Picre+'</em></p>' +
                '<p class="editeFun mt25"><a href="/consultantmanage/addservice/?ID='+list.ServiceList_Id+'">编辑</a>' +
                '<a href="javascript:void(0)" data-id="'+list.ServiceList_Id+'" class="operation">下架</a>' +
                '<a href="javascript:void(0)" data-id="'+list.ServiceList_Id+'" class="delete" data-title="'+list.ServiceList_Name+'">删除</a></p>' +
                '</div>' +
                '<a href="'+list.ServiceList_Url+'"><img src="'+list.ServiceList_Img+'" width="130" height="97"/></a>' +
                '<p class="tit"><a href="'+list.ServiceList_Url+'" target="_blank">'+list.ServiceList_Name+'</a></p>' +
                '<p class="nr mt10">服务简介：'+list.ServiceList_Depict+'</p>' +
                '</li>';
            $('#ServiceList').append(item);
        });
        //上下架操作
        ServiceOperation();

        //删除操作
        ServiceDelete();

    }else if(AjaxData.Status == '1'){
        var item;
        $.each(data.Data, function(i, list) {
            item = '<li>' +
                '<div class="BoxR">' +
                '<p class="price">￥<em>'+list.ServiceList_Picre+'</em></p>' +
                '<p class="editeFun mt25">审核中</p>' +
                '</div>' +
                '<a href="'+list.ServiceList_Url+'"><img src="'+list.ServiceList_Img+'" width="130" height="97"/></a>' +
                '<p class="tit"><a href="'+list.ServiceList_Url+'" target="_blank">'+list.ServiceList_Name+'</a></p>' +
                '<p class="nr mt10">服务简介：'+list.ServiceList_Depict+'</p>' +
                '</li>'
            $('#ServiceList').append(item);
        });
    }else if(AjaxData.Status == '0'){
        var item;
        $.each(data.Data, function(i, list) {
            item = '<li>' +
                '<div class="BoxR">' +
                '<p class="price">￥<em>'+list.ServiceList_Picre+'</em></p>' +
                '<p class="editeFun mt25">' +
                '<a href="javascript:void(0)" data-id="'+list.ServiceList_Id+'" class="operation">提交审核</a>' +
                '<a href="/consultantmanage/addservice/?ID='+list.ServiceList_Id+'">编辑</a>' +
                '</p>' +
                '</div>' +
                '<a href="'+list.ServiceList_Url+'"><img src="'+list.ServiceList_Img+'" width="130" height="97"/></a>' +
                '<p class="tit"><a href="'+list.ServiceList_Url+'" target="_blank">'+list.ServiceList_Name+'</a></p>' +
                '<p class="nr mt10">服务简介：'+list.ServiceList_Depict+'</p>' +
                '</li>'
            $('#ServiceList').append(item);
        });
        ServiceOperationB();
    }

    //分页机制
    if(data.PageCount >1){
        $("#Page").show();
        diffPage(data);
    }
}

//Ajax请求成功，但无数据
function DataFailure(AjaxData,Type) {
    $("#AddCase").hide();
    $("#List").hide();
    $("#NoServiceBox").show();
    if(Type == '0'){
        $("#NoServiceBox").find('.Nbtn').show();
        if(AjaxData.Status == '3'){
            $("#NoService").find('p').text('您还没有已上架的服务');
        }else if(AjaxData.Status == '1'){
            $("#NoService").find('p').text('您还没有正在审核的服务');
        }else if(AjaxData.Status == '0'){
            $("#NoService").find('p').text('草稿箱空空如也');
        }
    }else if(Type == '1'){
        $("#NoServiceBox").find('.Nbtn').hide();
        if(AjaxData.Status == '3'){
            $("#NoService").find('p').text('没有搜索到已上架的“'+AjaxData.Keyword+'”相关服务');
        }else if(AjaxData.Status == '1'){
            $("#NoService").find('p').text('没有搜索到正在审核的“'+AjaxData.Keyword+'”相关服务');
        }else if(AjaxData.Status == '0'){
            $("#NoService").find('p').text('草稿箱没有搜索到“'+AjaxData.Keyword+'”相关服务');
        }
    }
}

//上下架操作方法
function ServiceOperation() {
    $(".operation").click(function () {
        var AJaxData = {
            'Intention': 'ConsultantServiceListOperation',
            'id': $(this).attr('data-id')
        }
        $.post("/consultantmanageajax/",AJaxData,function(data){
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                setTimeout(function(){
                    window.location.reload();
                },500);
            }else {
                layer.msg(data.Message);
                setTimeout(function(){
                    window.location.reload();
                },500);
            }
        },'json');
    })
}

//提交审核操作方法
function ServiceOperationB() {
    $(".operation").click(function () {
        var AJaxData = {
            'Intention': 'ConsultantServiceSubmitAudit',
            'id': $(this).attr('data-id')
        }
        $.post("/consultantmanageajax/",AJaxData,function(data){
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                setTimeout(function(){
                    window.location.reload();
                },500);
            }else {
                layer.msg(data.Message);
                setTimeout(function(){
                    window.location.reload();
                },500);
            }
        },'json');
    })
}

//删除操作方法
function ServiceDelete() {
    $('.delete').click(function () {
        var title = $(this).attr('data-title')
        var id=$(this).attr('data-id');
        layer.confirm('您确定要删除<span style="color: red">'+title+'</span>？', {
            title: '删除提示',
            btn: ['确定','取消'] //按钮
        }, function(index){
            var AJaxData = {
                'Intention': 'ConsultantServiceListDelete',
                'id':id
            }
            $.post("/consultantmanageajax/",AJaxData,function(data){
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },500);
                }else {
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },500);
                }
            },'json');
            layer.close(index);
        });
    })
}