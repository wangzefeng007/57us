/**
 * Created by Foliage on 2017/1/3.
 */
$(function () {
    //学校申请添加行html代码
    var AddTrHtml = '<tr class="NewData">' +
        '<td height="30px"><p class="resultTex"></p><input type="text" name="SchoolName" class="SchoolName" value="" placeholder="学校名称" maxlength="30"/></td>' +
        '<td><p class="resultTex"></p><input type="text" name="MajorName" class="MajorName" value="" placeholder="课程/专业名称" maxlength="30"/></td>' +
        '<td><p class="resultTex"></p><input type="text" name="DeliveryTime" class="DeliveryTime" value="" onfocus="WdatePicker({skin:\'whyGreen\'})" readonly placeholder="投递时间"/></td>' +
        '<td><p class="resultTex"></p><input type="text" name="Status" class="Status" value="" placeholder="当前状态" maxlength="5"/></td>' +
        '<td><p class="resultTex"></p><input type="text" name="Remark" class="Remark" value="" placeholder="备注" maxlength="30"/></td>' +
        '<td class="white"><a href="javascript:void(0)" class="delete delete1">删除</a></td>' +
        '</tr>';

    //学校申请取消，发送时使用的html
    html2 = '<tr>' +
        '<th width="173">学校名称</th>' +
        '<th width="139">课程/专业名称</th>' +
        '<th width="123">投递时间</th>' +
        '<th width="94">状态</th>' +
        '<th width="137">备注</th>' +
        '<th width="84" class="white"></th>' +
        '</tr>';
    var SelectionState  = $("#SelectionState").text();

    //学校申请点击编辑事件
    $('.editeBtn1').on('click',function(){
        if(SelectionState == '已确认'){
            layer.msg('学生已经入读学校，无法再进行相关信息的编辑');
            return;
        }
        if($(".new").is('.edite')){
            var NewSchoolTable = $(".SchoolTable").html();
            if(NewSchoolTable == OldSchoolTable){
                $(".new").removeClass("edite");
                $(".editeBtn1").text('编辑');
            }else {
                layer.confirm('您还没有保存添加相关记录确认要退出吗?', {
                    btn: ['确认','取消'] //按钮
                }, function(index){
                    $(".SchoolTable").empty();
                    $(".SchoolTable").append(OldSchoolTable);
                    $(".new").removeClass("edite");
                    $(".editeBtn1").text('编辑');
                    layer.close(index);
                },function (index) {
                    layer.close(index);
                });
            }
        }else {
            if($(".SchoolTable tr").length < 2){
                $('.SchoolTable').append(AddTrHtml);
            }
            $(".new").addClass("edite");
            $(".editeBtn1").text('取消');
            $(document).on('change','.NewData input',function () {
                $(this).prev().text($(this).val());
                $(this).attr('value',$(this).val());
            })
            OldSchoolTable = $(".SchoolTable").html();
        }
    })

    //学校申请增加行
    $(document).on('click','#AddNewTr',function () {
        $('.SchoolTable').append(AddTrHtml);
    })

    //删除行
    $(document).on('click','.delete1',function () {
        var location = $(this).parent().parent().index();
        layer.confirm('您确认要删除此条记录吗?', {
            btn: ['确认','取消'] //按钮
        }, function(index){
            layer.msg('删除成功');
            $(".SchoolTable tr").eq(location).remove();
            layer.close(index);
        },function (index) {
            layer.close(index);
        });
    })

    //学校申请保存并发送
    $(document).on('click','#SchoolSave',function () {
        var SchoolData = [];
        $('.SchoolTable .NewData').each(function () {
            SchoolData.push({'SchoolName':$(this).find('.SchoolName').val(),'MajorName':$(this).find('.MajorName').val(),'DeliveryTime':$(this).find('.DeliveryTime').val(),'Status':$(this).find('.Status').val(),'Remark':$(this).find('.Remark').val()});
        })
        var ajaxData = {
            'Intention': 'SchoolApplySave', //方法
            'ID':$(".serviceProcess").attr('data-id'), //对应的id
            'SchoolData':SchoolData, //相关的数据， SchoolName 学校名称  MajorName 课程/专业名称  DeliveryTime 投递时间 Status 申请状态  Remark 备注
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajaxstudyconmanage.html",
            data: ajaxData,
            beforeSend: function () {
                //提交加载效果
                public_loading();
            },
            success: function(data) {
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },400);
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $("#public_loading").remove();
            }
        });
    })
    var OldSchoolTable;

    //录取学校添加行html代码
    var AddTrHtml2;
    AddTrHtml2 = '<tr class="EnrollData">' +
        '<td><p class="resultTex"></p><input type="text" name="AttendSchool" class="AttendSchool" value="" /></td>' +
        '<td><p class="resultTex"></p><input type="text" name="AttendMajor" class="AttendMajor" value="" /></td>' +
        '<td><p class="resultTex"></p><input type="text" name="PlanAttendDate" class="PlanAttendDate" value="" onfocus="WdatePicker({skin:\'whyGreen\'})" readonly/></td>' +
        '<td class="offerimg"><p class="resultTex"><a href="" download=""></a></p><span class="input"><a href="" download="" class="edittext"></a><a href="javascript:void(0);" class="fileimg uppic">上传<input type="file" name="uppic" class="addpreview" /></a></span></td>' +
        '<td class="white"><a href="javascript:void(0)" class="delete delete2">删除</a></td>' +
        '</tr>';

    //时间回调到前面的p标签框内
    $(document).on('focus','.DeliveryTime,.PlanAttendDate', function() {
        $(this).prev().text($(this).val());
        $(this).attr('value',$(this).val());
    })

    //上传图片，事件
    $(document).on('change','.addpreview',function () {
        var location = $(this).parents('tr').index();
        PreviewImage(this,location);
    })

    //肖像单张图片上传方法
    function PreviewImage(file,location) {
        if(file.files && file.files[0]) {
            var reader = new FileReader();
            //判断上传枨是否正确
            if(file.files[0].type != "image/jpeg" && file.files[0].type != "image/png" && file.files[0].type != "image/gif" && file.files[0].type != "image/bmp"){
                layer.alert('选择文件错误,图片类型必须是<span style="color: red">jpeg,jpg,png,gif,bmp中的一种</span>');
                $("#AddPortraits").show();
                return;
            }else if(file.files[0].size > 5242880){  //判断图片是否大于5Mb
                layer.alert('请不要上传大于5M的图片');
                $("#AddPortraits").show();
                return;
            }
            reader.onload = function(evt) {
                $(".SchoolTable2 tr").eq(location).find('.offerimg').find('.resultTex a').attr('href',evt.target.result);
                $(".SchoolTable2 tr").eq(location).find('.offerimg').find('.resultTex a').attr('download',file.files[0].name);
                $(".SchoolTable2 tr").eq(location).find('.offerimg').find('.resultTex a').text(file.files[0].name);
                $(".SchoolTable2 tr").eq(location).find('.offerimg').find('.edittext').text(file.files[0].name);
                $(".SchoolTable2 tr").eq(location).find('.offerimg').find('.edittext').attr('href',evt.target.result);
                $(".SchoolTable2 tr").eq(location).find('.offerimg').find('.edittext').attr('download',file.files[0].name);
                $(".SchoolTable2 tr").eq(location).find('.offerimg').find('.uppic').html('重新上传<input type="file" name="uppic" class="addpreview">');
            }
            reader.readAsDataURL(file.files[0]);
        }
    }

    //录取通知点击编辑事件
    $('.editeBtn2').on('click',function(){
        if(SelectionState == '已确认'){
            layer.msg('学生已经入读学校，无法再进行相关信息的编辑');
            return;
        }
        if($(".SchoolTable2 tr").length < 3){
            $('.SchoolTable2').append(AddTrHtml2);
        }
        if($(".EnrollSchool").is('.edite')){
            var NewEnrollTable = $(".SchoolTable2").html();
            if(NewEnrollTable == OldEnrollTable){
                $(".EnrollSchool").removeClass("edite");
                $(".editeBtn2").text('编辑');
            }else {
                layer.confirm('您还没有保存添加相关记录确认要退出吗?', {
                    btn: ['确认','取消'] //按钮
                }, function(index){
                    $(".SchoolTable2").empty();
                    $(".SchoolTable2").append(OldEnrollTable);
                    $(".EnrollSchool").removeClass("edite");
                    $(".editeBtn2").text('编辑');
                    layer.close(index);
                },function (index) {
                    layer.close(index);
                });
            }
        }else {
            $(".EnrollSchool").addClass("edite");
            $(".editeBtn2").text('取消');
            $(document).on('change','.EnrollData input',function () {
                $(this).prev().text($(this).val());
                $(this).attr('value',$(this).val());
            })
            OldEnrollTable = $(".SchoolTable2").html();
        }
    })

    //录取通知增加行
    $('#AddNewTr2').on('click',function () {
        $('.SchoolTable2').append(AddTrHtml2);
    })

    //录取通知删除行
    $(document).on('click','.delete2',function () {
        var location = $(this).parent().parent().index();
        layer.confirm('您确认要删除此条记录吗?', {
            btn: ['确认','取消'] //按钮
        }, function(index){
            layer.msg('删除成功');
            $(".SchoolTable2 tr").eq(location).remove();
            layer.close(index);
        },function (index) {
            layer.close(index);
        });
    })

    //录取学校保存并发送
    $(document).on('click','#EnrollSave',function () {
        var EnrollData = [];
        $('.SchoolTable2 .EnrollData').each(function () {
            EnrollData.push({'AttendSchool':$(this).find('.AttendSchool').val(),'AttendMajor':$(this).find('.AttendMajor').val(),'PlanAttendDate':$(this).find('.PlanAttendDate').val(),'OfferImgUrl':$(this).find('.edittext').attr('href'),'OfferImgName':$(this).find('.edittext').attr('download')});
        })
        var ajaxData = {
            'Intention': 'EnrollSchoolSave', //方法
            'ID':$(".serviceProcess").attr('data-id'), //对应id
            'SchoolData':EnrollData, //录取学校数据  AttendSchool 入读学校名称  AttendMajor 入读专业名称  PlanAttendDate 计划入学时间  OfferImgUrl Offer录取证书 base64编码  OfferImgName offer的图片名称
        }

        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajaxstudyconmanage.html",
            data: ajaxData,
            beforeSend: function () {
                //提交加载效果
                public_loading();
            },
            success: function(data) {
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },400);
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $("#public_loading").remove();
            }
        });
    })
    var OldEnrollTable;
})