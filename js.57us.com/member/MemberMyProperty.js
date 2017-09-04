/**
 * Created by Foliage on 2017/2/9.
 */
$(function () {
    //初始化加载明细数据
    pageLoad(0);

    //收入明细类型
    $("#detailType li").on('click',function () {
        $("#detailType li").removeClass('on');
        $(this).addClass('on');
        pageLoad(0);
    })

    //提现窗口显示
    $("#withdraw").on('click',function () {
        $(".mpoptrans").show();
        popCenterWindow('mpop','3.3','2');
    })

    //提现窗口关闭
    $(".canceBtn").on('click',function () {
        $(".mpoptrans").hide();
    })

    //提现提交
    $(".withdrawBtn").on('click',function () {
        // ajax提交参数
        var ajaxData = {
            'Intention': 'WithdrawDeposit', //资金提现方法
            'TransactionType':$("#transactionType .rb_active").attr('val'), //提现类型 0代表支付宝
            'TransactionAccount':$("#transactionAccount").val(), //提现账号
            'TransactionMoney':$("#transactionMoney").val(), //提现金额
        }

        //执行验证
        if(ajaxData.TransactionAccount == ''){
            layer.msg('提现账号不能为空');
            return
        }else if(ajaxData.TransactionMoney == ''){
            layer.msg('提现金额不能为空');
            return
        }else if($("#maxMoney").val() < ajaxData.TransactionMoney){
            layer.msg('提现金额不能大于'+$("#maxMoney").val()+'');
            console.log(ajaxData.TransactionMoney);
            return
        }

        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode == '200'){
                    layer.msg('申请提现成功');
                    $(".mpoptrans").hide();
                    setTimeout(function(){
                        window.location.reload();
                    },500);
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })
})

/**
 * 函数描述
 *
 * @param id 参数 模态窗口的id
 * @param w 参数 根据模态窗口大小调整距离left
 * @param h 参数 根据模态窗口大小调整距离top
 */
function popCenterWindow(id,w,h) {
    //获取窗口的高度
    var windowHeight = $(window).height();
    //获取窗口的宽度
    var windowWidth = $(window).width();
    //计算弹出窗口的左上角Y的偏移量
    var popY = windowHeight / w;
    var popX = windowWidth / h;
    //设定窗口的位置
    $("."+id+"").css("top", popY).css("left", popX);
}

//明细类型加载
function  pageLoad(page) {
    var ajaxData = {
        'Intention': 'MoneyDetails', //资金明细加载列表
        'MoneyType':$("#detailType .on").attr('data-type'), //资金明细类型 0代表全部 1代表收入 2代表支出 3代表提现记录
        'Page':page, //页码
    }

    $.ajax({
        type: "post",
        dataType: "json",
        url: "/userajax.html",
        data: ajaxData,
        beforeSend: function () {
            layer.load(2);
        },
        success: function(data) {
            if(data.ResultCode == '200'){
                pageLoadSucceed(data);
            }else if(data.ResultCode == '201'){
                //清空数据
                $(".dealist").remove();
                $("#noTrans").show();
            }else{
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            layer.closeAll('loading');
        }
    });
}

//加载成功
function pageLoadSucceed(data) {
    //清空数据
    $(".dealist").remove();
    //隐藏没有资金明细
    $("#noTrans").hide();
    //数据注入
    var item;
    $.each(data.Data, function(i, list) {
        item = '<tr class="dealist">' +
            '<td>'+list.List_Time+'</td>' +
            '<td>'+list.List_Details+'</td>' +
            '<td>'+list.List_Money+'</td>' +
            '<td>'+list.List_Status+'</td>' +
            '</tr>';
        $('#dealLists').append(item);
    });

    //页码
    $(".tcdPageCode").createPage({
        pageCount:data.PageCount,
        current:data.Page,
        backFn:function(p){
            pageLoad(p);
        }
    });
}