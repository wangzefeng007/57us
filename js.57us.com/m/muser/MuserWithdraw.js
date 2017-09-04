/**
 * Created by Foliage on 2017/2/22.
 */
$(document).ready(function () {
    'use strict';
    //金额提现
    $(document).on("pageInit", "#withdraw", function(e, id, page) {
        var withdrawBtn = page.find('.withdrawBtn');
        withdrawBtn.on('click',function () {
            var maxMoney = page.find('.maxMoney').attr('data-val');
            var ajaxData = {
                'Money':page.find('.money').val(), //提现金额
                'Accounts':page.find('.account').val(), //支付宝账号
            }
            var tx_html = '';
            tx_html +='<div class="sureHtml">'
            tx_html +='<table border="0" cellspacing="0" cellpadding="0" width="100%" class="txTab">';
            tx_html +='<tr>';
            tx_html +='<th width="">提现账号：</th>';
            tx_html +='<td>'+ajaxData.Accounts+'</td>';
            tx_html +='</tr>';
            tx_html +='<tr>';
            tx_html +='<th>提现金额：</th>';
            tx_html +='<td>'+ajaxData.Money+'元</td>';
            tx_html +='</tr>';
            tx_html +='</table>';
            tx_html +='</div>';
            if(ajaxData.Accounts == ''){
                $.toast('支付付账号不能为空');
                return
            }else if(ajaxData.Money == ''){
                $.toast('提现金额不能为空');
                return
            }else if(parseFloat(ajaxData.Money) > parseFloat(maxMoney)){
                $.toast('提现金额不能超过'+maxMoney);
                return
            }
            $.confirm(tx_html, function () {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "/member/withdraw/",
                    data: ajaxData,
                    beforeSend: function () {
                        $.showPreloader('提交中');
                    },
                    success: function (data) {
                        if(data.ResultCode == '200'){
                            $.toast(data.Message);
                            setTimeout(function() {
                                window.location.reload();
                            },500);
                        }else{
                            $.toast(data.Message);
                        }
                    },complete: function () {
                        $.hidePreloader();
                    }
                })
            });
        })
    });
    $.init();
})
