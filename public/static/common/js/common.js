
/*获得今天的日期，自动补0*/
function getNowDate() {
    var mydate = new Date();
    var year = mydate.getFullYear();
    var mouth = mydate.getMonth() + 1;
    var day = mydate.getDate();
    var today = '';

    today += year + '-';

    if (mouth >= 10) {
        today += mouth + '-';
    } else {
        today += '0' + mouth + '-';
    }

    if (day >= 10) {
        today += day;
    } else {
        today += '0' + day;
    }

    return today;
}

//去除虚线
function delLine(obj) {
    $(obj).focus(function () {
        $(this).blur();
    });
}
delLine('a');
delLine('button');


//检查是否为手机号
telRuleCheck2 = function (string) {
    var pattern = /^1[34578]\d{9}$/;
    if (pattern.test(string)) {
        return true;
    }
    console.log('check mobile phone ' + string + ' failed.');
    return false;
};
/* 检查是手机还是pc*/
function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
        "SymbianOS", "Windows Phone",
        "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}
var flag = IsPC(); //true为PC端，false为手机端

var win = $(window);

/*5秒后统计网站访问量*/
function addLog(url) {
    setTimeout(function () {
        $.get(url,{},function (data) {
            console.log(data);
        })
    }, 5000);

    return true;
}


$(function () {

    /* 中英文显示*/
    var lang = $.cookie('think_var');
    var cn = $('#cn');
    var en = $("#en");
    switch (lang){
        case 'zh-cn':
            cn.hide();
            break;
        case 'en-us':
            en.hide();
            break;
    }


    /*loading */
    /*$('#loading').hide();*/


    /*页脚按钮悬浮效果*/
    $('.a-link').hover(function () {
        $(this).before().animate({
            left:0
        });
    });

    var date = new Date;
    var year = date.getFullYear();
    $('#foot-year').html(year);


});

