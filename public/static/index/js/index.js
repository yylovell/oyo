
$(function () {
    //calling jPreLoader
    var timer;
    $('body').jpreLoader({
        splashID: "#jSplash",
        loaderVPos: '70%',
        autoClose: true,
        closeBtnText: '点击进入',
        splashFunction: function () {
            //passing Splash Screen script to jPreLoader
            $('#jSplash').children('section').not('.selected').hide();
            $('#jSplash').hide().fadeIn(100);

            timer = setInterval(function () {
                splashRotator();
            }, 3000);
        }
    }, function () {	//callback function
        clearInterval(timer);

    });

    //create splash screen animation
    function splashRotator() {
        var cur = $('#jSplash').children('.selected');
        var next = $(cur).next();

        if ($(next).length != 0) {
            $(next).addClass('selected');
        } else {
            $('#jSplash').children('section:first-child').addClass('selected');
            next = $('#jSplash').children('section:first-child');
        }

        $(cur).removeClass('selected').fadeOut(100, function () {
            $(next).fadeIn(100);
        });
    }

    /*滚动实现*/
    jQuery.scrollto = function (scrolldom, scrolltime) {

        $(scrolldom).click(function () {
            var scrolltodom = $(this).attr("date-scroll");
            $(this).addClass("thisscroll").parent().siblings().find('a').removeClass("thisscroll");
            $('html,body').animate({
                    scrollTop: $(scrolltodom).offset().top - 70
                }, scrolltime
            );
            return false;
        });

    };
    $.scrollto("#scrollnav a", 600);
    $.scrollto(".about-us", 600);


    /*动画效果配置*/
    $('.aniview').AniView({
        animateThreshold: 100,
        scrollPollInterval: 20
    });
    /*$('.about-us').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
     $(this).removeClass('hinge');
     });*/

    /*案例card  hover*/
    $('.tab-content a').hover(function () {
        var $this = $(this);

        $this.find('.thum-card-header').addClass('animated fadeInDown');
        $this.find('.thum-card-btn').addClass('animated fadeInUp');
    }, function () {
        var $this = $(this);

        $this.find('.thum-card-header').removeClass('animated fadeInDown');
        $this.find('.thum-card-btn').removeClass('animated fadeInUp');
    });



});
