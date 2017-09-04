/**
 * Created by Foliage on 2016/12/20.
 */
//页面加载完成后注入免费评估代码，及相关dom操作
$(document).ready(function(){
    //免费评估注入到右侧广告上方
    $(".SlideRight .adList").before(assesshtml);
    //获取方案下拉框美化
    $('div[name="class"],div[name="scrol"]').inputbox({
        height:37,
        width:238
    });
    $('div[name="applyItem"]').inputbox({
        height:37,
        width:238
    });

    //免费评估下拉值改变时，根据情况给获取方案按钮添加class
    $('#Project a,#Grade a,#Results a').on('click',function () {
        addapplication();
    })

    //点击显示模态窗口
    $('#application').click(function () {
        if($("#Project input").val() == '申请项目'){
            layer.msg('请选择申请项目');
            return
        }else if($("#Grade input").val() == '目前就读年级'){
            layer.msg('请选择目前就读年级');
            return
        }else if($("#Results input").val() == '国内平均成绩'){
            layer.msg('请选择目前成绩');
            return
        }
        $(".mask").fadeIn();
        $(".getProgram").fadeIn();
    })

    //点击关闭模态窗口
    $(".getProgram .close").click(function () {
        $(".mask").fadeOut();
        $(".getProgram").fadeOut();
    })

    //点击关闭模态窗口
    $(".getProgram .close").click(function () {
        $(".mask").fadeOut();
        $(".getProgram").fadeOut();
    })
    //点击模态窗口确定按钮，ajax提交数据
    $(".surebtn").click(function () {
        if(rule.phone.test($('#tel').val()) != true){
            layer.msg('请输入正确的手机号码');
            return
        }
        ajaxData = {
            'Intention': 'IndexApply', //方法
            'Project':$("#Project input").val(), //申请项目
            'Grade':$("#Grade input").val(),  //目前就读年级
            'Results':$("#Results input").val(), //国内平均成绩
            'Tel':$('#tel').val(),
        }
        $.post("/commonajax/",ajaxData,function(data){
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                $('#tel').val('');
                $(".mask").fadeOut();
                $(".getProgram").fadeOut();
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })
})

//获取方案，添加class方法
function addapplication() {
    var _Project = $("#Project input").val();
    var _Grade = $("#Grade input").val();
    var _Results = $("#Results input").val();
    if(_Project != '申请项目' && _Grade != '目前就读年级' && _Results != '国内平均成绩'){
        $("#application").addClass('on');
    }else {
        $("#application").removeClass('on');
    }
}

var assesshtml = '<div class="toolStudy cf">'+
    '<p class="title">免费评估</p>'+
    '<div class="toolStudyM">'+
    '<div name="applyItem" type="selectbox" class="DiySelect" id="Project">'+
    '<div class="opts">'+
    '<a href="javascript:void(0);" value="博士">博士</a>'+
    '<a href="javascript:void(0);" value="硕士">硕士</a>'+
    '<a href="javascript:void(0);" value="本科">本科</a>'+
    '<a href="javascript:void(0);" value="高中">高中</a>'+
    '<a href="javascript:void(0);" value="小学">小学</a>'+
    '<a href="javascript:void(0);" value="语言课程">语言课程</a>'+
    '<a href="javascript:void(0);" value="单签证">单签证</a>'+
    '<a href="javascript:void(0);" value="申请项目" class="selected">申请项目</a>'+
    '</div>'+
    '</div>'+
    '<div name="class" type="selectbox" class="DiySelect" id="Grade">'+
    '<div class="opts">'+
    '<a href="javascript:void(0);" value="本科毕业及以后">本科毕业及以后</a>'+
    '<a href="javascript:void(0);" value="大四">大四</a>'+
    '<a href="javascript:void(0);" value="大三">大三</a>'+
    '<a href="javascript:void(0);" value="大二">大二</a>'+
    '<a href="javascript:void(0);" value="大一">大一</a>'+
    '<a href="javascript:void(0);" value="高中毕业已工作">高中毕业已工作</a>'+
    '<a href="javascript:void(0);" value="高三">高三</a>'+
    '<a href="javascript:void(0);" value="高二">高二</a>'+
    '<a href="javascript:void(0);" value="高一">高一</a>'+
    '<a href="javascript:void(0);" value="初三">初三</a>'+
    '<a href="javascript:void(0);" value="初二及以前">初二及以前</a>'+
    '<a href="javascript:void(0);" value="目前就读年级" class="selected">目前就读年级</a>'+
    '</div>'+
    '</div>'+
    '<div name="scrol" type="selectbox" class="DiySelect" id="Results">'+
    '<div class="opts">'+
    '<a href="javascript:void(0);" value="95 - 100">95 - 100</a>'+
    '<a href="javascript:void(0);" value="90 - 94">90 - 94</a>'+
    '<a href="javascript:void(0);" value="82 - 84">82 - 84</a>'+
    '<a href="javascript:void(0);" value="78 - 81">78 - 81</a>'+
    '<a href="javascript:void(0);" value="75 - 77">75 - 77</a>'+
    '<a href="javascript:void(0);" value="68 - 71">68 - 71</a>'+
    '<a href="javascript:void(0);" value="68及以下">68及以下</a>'+
    '<a href="javascript:void(0);" value="国内平均成绩" class="selected">国内平均成绩</a>'+
    '</div>'+
    '</div>'+
    '<input type="button" name="application" id="application" value="获取申请方案" class="btn mt20" />'+
    '<div class="mask"></div>'+
    '<div class="getProgram">'+
    '<span class="close">关闭</span>'+
    '<input type="text" name="tel" id="tel" value="" class="input" placeholder="留下电话，方便沟通。" />'+
    '<div class="btn tar">'+
    '<!--<a href="javascript:void(0)" class="no fl close2">暂不</a>-->'+
    '<a href="javascript:void(0)" class="sure fr surebtn">确认</a>'+
    '</div>'+
    '</div>'+
    '</div>'+
    '</div>';



