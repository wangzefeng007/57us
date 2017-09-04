/**
 * Created by Foliage on 2017/1/16.
 */
$(function () {
    //战队饼图
    $(".teamList li").each(function() {
        var piepic = $(this).find(".piePic");
        var num = piepic.attr("data-text");
        var zfnum = $(this).find(".zf .proportion").attr("data-num");
        var ffnum = $(this).find(".ff .proportion").attr("data-num");
        var dom = document.getElementById("main" + num);
        var myChart = echarts.init(dom);
        var app = {};
        option = null;
        app.title = '战队环形图';

        option = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'left',
                data: ['正方：需要', '反方：不需要']
            },
            series: [{
                name: '战队',
                type: 'pie',
                radius: ['60%', '70%'],
                avoidLabelOverlap: true,
                label: {
                    normal: {
                        show: true,
                        position: 'center'
                    },
                    emphasis: {
                        show: false,
                        textStyle: {
                            fontSize: '16',
                            fontWeight: 'bold',
                            backgroundColor: '#FFF'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data: [{
                    value: zfnum,
                    itemStyle: {
                        normal: {
                            color: '#ff6767'
                        }
                    },
                    name: '需要'
                }, {
                    value: ffnum,
                    itemStyle: {
                        normal: {
                            color: '#29beb5'
                        }
                    },
                    name: '不需要'
                }]
            }]
        };;
        if(option && typeof option === "object") {
            myChart.setOption(option, true);
        }
    })
})