/**
 * Created by Foliage on 2016/10/17.
 */
//提现现弹窗
$(".reflect a").click(function(){
    html = '<div class="reflectBox ReflectBox">' +
	'<div class="reflecTab">' +
		'<table border="0" cellspacing="0" cellpadding="0" width="100%">' +
			'<tr>' +
				'<th>提现到：</th>' +
				'<td>' +
					'<div class="labelList">' +
						'<label name="studyStyle" type="radiobox" val="支付宝" class="cbt checked rb" spellcheck="true"><i></i>支付宝</label>' +
					'</div>' +
				'</td>' +
			'</tr>' +
			'<tr>' +
				'<th>提现帐号：</th>' +
				'<td>' +
					'<div class="inputBox">' +
						'<input type="text" name="alipaymember" id="alipaymember" value="" class="InsInput alipaymember" />' +
					'</div>' +
				'</td>' +
			'</tr>' +
			'<tr>' +
				'<th>提现金额：</th>' +
				'<td>' +
					'<div class="inputBox">' +
						'<input type="text" name="money" id="money" value="" class="InsInput" placeholder="请输入提现金额" />' +
					'</div>' +
					'<p class="c9 mt5">预计1-3个工作日到帐</p>' +
				'</td>' +
			'</tr>' +
		'</table>' +
	'</div>' +
'</div>';
    layer.open({
        type: 1,
        title:"余额提现",
        skin: 'reflectBox', //样式类名
        closeBtn: 0, //不显示关闭按钮
        shift: 2,
        area: ['400px', '370px'],
        btn: ['取消', '提现'],
        shadeClose: true, //开启遮罩关闭
        content:html,
        btn1: function(index, layero){
            layer.close(index);
        },
        btn2: function(index, layero){
            var AjaxData = {
                'Intention': 'CustomerManageAssets',
                'alipaymember':$(".reflectBox #alipaymember").val(),
                'money':$(".reflectBox #money").val(),
            }
            $.ajax({
                type: "post",	//提交类型
                dataType: "json",	//提交数据类型
                url: "/commonajax/",  //提交地址
                data: AjaxData,
                success: function(data) {	//函数回调
                    if(data.ResultCode == "200"){
                        layer.msg(data.Message);
                        setTimeout(function(){
                            window.location.reload();
                        },500);
                    }else if(data.ResultCode == "201"){
                        layer.msg(data.Message);
                    }else if(data.ResultCode == "202"){
                        layer.msg(data.Message);
                    }else if(data.ResultCode == "101"){
                        layer.msg(data.Message);
                    }else if(data.ResultCode == "102"){
                        layer.msg(data.Message);
                    }
                },
            });
        },
        success: function(layero){
            $(".labelList label").inputbox()
        }
    });
})

