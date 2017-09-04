/**
 * Created by Foliage on 2016/10/11.
 */

$(function () {
    //服务列表初始化加载
    Ajax();
    //点击搜素时
    $("#CourseSearchBtn").on('click',function () {
        Ajax();
    })

    //菜单点击时
    $("#CaseMenu li").on('click',function () {
        $("#CaseMenu li").removeClass();
        $(this).addClass('on');
        $("#CourseSearch").val('');
        $("#CourseSearch").attr('value','');
        Ajax();
    })

})

function Ajax(Page) {
    var AjaxData = {
        'Intention': 'TearchCourseList',
        'Page':Page,
        'Status':$("#CaseMenu .on").find('a').attr('id'),
        'Keyword':$("#CourseSearch").val(),
    }
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/teachermanageajax/",  //提交地址
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
    $("#CourseList").empty();
    $("#NoServiceBox").hide();
    $("#AddCase").show();
    $("#List").show();
    var item;
    if(AjaxData.Status == '3'){ //已发布
        $.each(data.Data, function(i, list) {
            item = '<li>' +
                '<div class="BoxR">' +
                '<p class="price">￥<em>'+list.CourseList_Picre+'</em></p>' +
                '<p class="editeFun mt25"><a href="/teachermanage/courseadd/?ID='+list.CourseList_Id+'">编辑</a>' +
                '<a href="javascript:void(0)" data-id="'+list.CourseList_Id+'" class="operation">下架</a>' +
                '<a href="javascript:void(0)" data-id="'+list.CourseList_Id+'" class="delete" data-title="'+list.CourseList_Name+'">删除</a></p>' +
                '</div>' +
                '<a href="'+list.CourseList_Url+'"><img src="'+list.CourseList_Img+'" width="130" height="97"/></a>' +
                '<p class="tit"><a href="'+list.CourseList_Url+'">'+list.CourseList_Name+'</a></p>' +
                '<p class="nr mt10">课程简介：'+list.CourseList_Depict+'</p>' +
                '</li>';
            $('#CourseList').append(item);
        });
        //上下架操作
        CourseOperation();
        //删除操作
        CourseDelete();
    }else if(AjaxData.Status == '1'){ //审核中
        $.each(data.Data, function(i, list) {
            item = '<li>' +
                '<div class="BoxR">' +
                '<p class="price">￥<em>'+list.CourseList_Picre+'</em></p>' +
                '<p class="editeFun mt25">审核中</p>' +
                '</div>' +
                '<a href="'+list.CourseList_Url+'"><img src="'+list.CourseList_Img+'" width="130" height="97"/></a>' +
                '<p class="tit"><a href="'+list.CourseList_Url+'">'+list.CourseList_Name+'</a></p>' +
                '<p class="nr mt10">课程简介：'+list.CourseList_Depict+'</p>' +
                '</li>'
            $('#CourseList').append(item);
        });
    }else if(AjaxData.Status == '0'){ //草稿箱
        $.each(data.Data, function(i, list) {
            item = '<li>' +
                '<div class="BoxR">' +
                '<p class="price">￥<em>'+list.CourseList_Picre+'</em></p>' +
                '<p class="editeFun mt25">' +
                '<a href="javascript:void(0)" data-id="'+list.CourseList_Id+'" class="audit">提交审核</a>' +
                '<a href="/teachermanage/courseadd/?ID='+list.CourseList_Id+'">编辑</a>' +
                '</p>' +
                '</div>' +
                '<a href="'+list.CourseList_Url+'"><img src="'+list.CourseList_Img+'" width="130" height="97"/></a>' +
                '<p class="tit"><a href="'+list.CourseList_Url+'">'+list.CourseList_Name+'</a></p>' +
                '<p class="nr mt10">课程简介：'+list.CourseList_Depict+'</p>' +
                '</li>'
            $('#CourseList').append(item);
        });
        //上下架操作
        CourseSubmitAudit();
    }

    //分页机制
    if(data.PageCount > 1){
        diffPage(data);
        $("#Page").show();
    }else {
        $("#Page").hide();
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
            $("#NoService").find('p').text('您还没有已上架的课程');
        }else if(AjaxData.Status == '1'){
            $("#NoService").find('p').text('您还没有正在审核的课程');
        }else if(AjaxData.Status == '0'){
            $("#NoService").find('p').text('草稿箱空空如也');
        }
    }else if(Type == '1'){
        $("#NoServiceBox").find('.Nbtn').hide();
        if(AjaxData.Status == '3'){
            $("#NoService").find('p').text('没有搜索到已上架的“'+AjaxData.Keyword+'”相关课程');
        }else if(AjaxData.Status == '1'){
            $("#NoService").find('p').text('没有搜索到正在审核的“'+AjaxData.Keyword+'”相关课程');
        }else if(AjaxData.Status == '0'){
            $("#NoService").find('p').text('草稿箱没有搜索到“'+AjaxData.Keyword+'”相关课程');
        }
    }
}

//上下架操作方法
function CourseOperation() {
    $(".operation").click(function () {
        var AJaxData = {
            'Intention': 'TearchCourseListOperation',
            'id': $(this).attr('data-id')
        }
        $.post("/teachermanageajax/",AJaxData,function(data){
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
function CourseDelete() {
    $('.delete').click(function () {
        var title = $(this).attr('data-title')
        var id=$(this).attr('data-id');
        layer.confirm('您确定要删除<span style="color: red">'+title+'</span>？', {
            title: '删除提示',
            btn: ['确定','取消'] //按钮
        }, function(index){
            var AJaxData = {
                'Intention': 'TearchCourseListDelete',
                'id':id
            }
            $.post("/teachermanageajax/",AJaxData,function(data){
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

//提交审核操作方法
function CourseSubmitAudit() {
    $(".audit").click(function () {
        var AJaxData = {
            'Intention': 'TeacherCourseSubmitAudit',
            'id': $(this).attr('data-id')
        }
        $.post("/teachermanageajax/",AJaxData,function(data){
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                setTimeout(function(){
                    window.location="/teachermanage/courselist/?S=0";
                },500);
            }else {
                layer.msg(data.Message);
                setTimeout(function(){
                    window.location="/teachermanage/courselist/?S=0";
                },500);
            }
        },'json');
    })
}