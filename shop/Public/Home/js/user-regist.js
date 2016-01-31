$(function (){
    // 登录注册切换
    $('#by-phone').click(function (){
        $(this).addClass('active');
        $('#by-email').removeClass('active');
        $('form[name="regist-by-mobile"]').show();
        $('form[name="regist-by-email"]').hide();
    });

    $('#by-email').click(function (){
        $(this).addClass('active');
        $('#by-phone').removeClass('active');
        $('form[name="regist-by-mobile"]').hide();
        $('form[name="regist-by-email"]').show();
    });


    // ========================== 手机注册 表单验证================================
    // 检测手机号是否存在
    $('#mobile-num').blur(function () {
        checkMobile();
    });

    // 检测密码是否合法
    $('#mobile-pwd').blur(function (){
        checkPassword(this);
    });

    // 检测重复密码
    $('#mobile-repwd').blur(function (){
        checkRePassword(this, $('#mobile-pwd'));
    });

    // 检查手机号
    function checkMobile ()
    {
        // 获取号码
        var obj    = $('#mobile-num');
        var oBtn   = $('#verify-btn');
        var number = $('#mobile-num').val();
        var oTxt   = $('#mobile-num').next('span');
        if (number.length == 0)
        {
            oTxt.css({
                'font-size':'12px',
                'color':'red'
            }).html('对不起，手机号不能为空');
            obj.attr('mobile-exists', '0');
            oBtn.attr('disabled', 'disabled');
            return false;
        }

        // 检测号码是否合法
        if ( ! number.match(/^[1][3578]{1}\d{9}$/))
        {
            oTxt.css({
                'font-size':'12px', 
                'color': 'red',
            }).html('对不起，手机号码不合法');
            obj.attr('mobile-exists', '0');
            oBtn.attr('disabled', 'disabled');
            return false;
        }
        else
        {
            // 验证手机号码是否存在
            $.ajax({
                'url': url + '/checkMobileExists',
                'type': 'GET',
                'data':{
                    'number':number
                },
                'dataType':'json',
                'success': function (d){
                    if (d == 1)
                    {
                        // 可用
                        obj.attr('mobile-exists', '1');
                        oBtn.removeAttr('disabled');
                        oTxt.css({
                            'font-size': '12px',
                            'color': 'green'
                        }).html('手机号码可用');
                    }
                    else
                    {
                        // 不可用
                        obj.attr('mobile-exists', '0');
                        oBtn.attr('disabled', 'disabled');
                        oTxt.css({
                            'font-size': '12px',
                            'color':'red',
                        }).html('该手机号已被占用');
                    }
                }
            });
        }
    }

    // Ajax去验证手机验证码
    $('#verify-btn').click(function ()
    {
        var data = $('#mobile-num').val();
        $.post(url + '/checkBySms', {'phone_number': data}, function (d){
            if (d != null)
            {
                $('#verify-btn').attr('disabled', 'disabled');
                var time = 120;
                var timer = null;
                timer = setInterval(function (){
                    var str = '计时' + time + '秒';
                    $('#verify-btn').html(str);
                    time--;
                    if (time == 0) 
                    {
                        $('#verify-btn').removeAttr('disabled');
                        clearInterval(timer);
                        $('#verify-btn').html('获取验证码');
                    }
                }, 1000);
            }
        });
        return false;
    });

    $('#regist-by-mobile').submit(function (){
        checkMobile();
        var checkP = checkPassword($('#mobile-pwd'));
        var checkRP = checkRePassword($('#mobile-repwd'), $('#mobile-pwd'));
        // 去查找手机号
        var m = $('#mobile-num').attr('mobile-exists');
        if (m == 0)
        {
            return false;
        }
        // 检测验证码
        var v = $('#verify-num').val()
        if (v.length < 6)
        {
            alert('验证码不合法');
            return false;
        }
        // 协议是否勾选
        if ( ! $('#mobile-agreement')[0].checked)
        {
            alert('请先阅读并勾选用户注册协议');
            return false;
        }
        if (checkP && checkRP)
        {
            return true;
        }
        return false;
    });


    // ========================== 邮箱注册 表单验证================================
    // 验证邮箱
    // 验证邮箱没有返回值，是修改了其属性性
    $('#email').blur(function (){
        checkEmail(this);
    });

    // 验证密码
    var checkEmailP
    $('#email-pwd').blur(function (){
        checkEmailP = checkPassword(this);
    });

    // 验证重复密码
    var checkEmailReP
    $('#email-repwd').blur(function (){
        var orgObj = $('#email-pwd');
        checkEmailReP = checkRePassword(this, orgObj);
    });


    // 验证验证码
    var checkCaptate = false;
    $('#code').blur(function () {
        if ($(this).val().length != 0)
        {
            checkCaptate = true;
        }
        else
        {
            $(this).parent().addClass('has-error');
            $(this).next('div').html('验证码不能为空').css('color', 'red');
        }
    });

    // 提交表单
    $('form[name="regist-by-email"]').submit(function (){
        // alert('');
        if ($('#email').attr('email-available') == 0)
        {
            $('#email').parent().addClass('has-error');
            $('#email').next('span').html('邮箱账号不可用，请重新填写').css({'color':'red', 'font-size':'12px'});
            return false;
        }
        else
        {
            if ( ! $('#email-agreement')[0].checked)
            {
                return false;
            }
            else
            {
                if (checkEmailP && checkEmailReP && checkCaptate)
                {
                    return true;
                }
            }
            
        }
        return false;
    });


});

// 是否同意注册协议


// 验证重复密码
function checkRePassword (obj, orgObj)
{
    var bool = false;
    if ($(obj).val().length == 0)
    {
        $(obj).parent().addClass('has-error');
        $(obj).next('span').html('重复密码不能为空').css({'color':'red', 'font-size':'12px'});
    }
    else if ($(obj).val() != orgObj.val()) 
    {
        $(obj).parent().addClass('has-error');
        $(obj).next('span').html('两次密码不一致').css({'color':'red', 'font-size':'12px'});
    }
    else
    {
        $(obj).parent().removeClass('has-error');
        $(obj).next('span').html('密码合法').css({'color':'green', 'font-size':'12px'});
        bool = true;
    }
    return bool;
}

// 验证密码
function checkPassword (obj)
{
    var bool = false;
    var counts = 0;
    if ($(obj).val() == 0)
    {
        $(obj).parent().addClass('has-error');
        $(obj).next('span').html('不能为空').css({'color': 'red', 'font-size': '12px'});
    }
    else if ($(obj).val().length >16 || $(obj).val().length < 8)
    {
        $(obj).parent().addClass('has-error');
        $(obj).next('span').html('密码长度在8到16位之间').css({'color': 'red', 'font-size': '12px'});
    }
    else 
    {
        // 密码安全等级
        if ($(obj).val().match(/\d+/))
        {
            counts++;
        }

        if ($(obj).val().match(/[a-zA-Z]+/))
        {
            counts++;
        }
        if ($(obj).val().match(/[^\da-zA-Z]+/))
        {
            counts++;
        }
        
        if (counts < 2)
        {
            $(obj).parent().addClass('has-error');
            $(obj).next('span').html('密码至少包含两种字符').css({'color': 'red', 'font-size': '12px'});
        }
        else 
        {
            $(obj).parent().removeClass('has-error');
            $(obj).next('span').html('密码合法').css({'color': 'green', 'font-size': '12px'});
            bool = true;
        }
    }
    return bool;
}

// 验证手机号
function checkMobile(obj) 
{
    var bool = false;
    if ($(obj).val().length == 0)
    {
        $(obj).parent().addClass('has-error');
        $(obj).next('span').html('不能为空').css('color', 'red');
    }
    else if ( ! $(obj).val().match(/^1[3587]{1}\d{9}$/))
    {
        $(obj).parent().addClass('has-error');
        $(obj).next('span').html('非法号码').css('color', 'red');
    }
    else 
    {
        $(obj).parent().removeClass('has-error');
        $(obj).next('span').html('合法手机号码').css('color', 'green');
        bool = true;
    }
    return bool;
}

// 检测并用Ajax请求检测该邮箱是否已经注册
function checkEmail(obj)
{

    if ( ! $(obj).val().match(/^[a-zA-Z1-9]+\w+@[a-zA-Z0-9]+\.\w+[\da-zA-Z]$/))
    {
        $(obj).next('span').html('账号不能包含特殊字符，并且不能以下划线开头或结尾').css({'color':'red', 'font-size':'12px'});
    }
    else
    {
        // Ajax请求判断邮箱是否可用
        var emailValue = $('email').val();
        $.post(url + '/checkEmailAvailable', {'email':emailValue}, function (d){
            if (d == 'no')
            {
                $(obj).parent().addClass('has-error');
                $(obj).next('span').html('该邮箱已被注册').css({'color':'red', 'font-size':'12px'});
                // 由于在Ajax里面封装的方法不会返回值，因此这里修改input标签的属性，当提交表单的时候通过这个属性的值来判断
                $(obj).attr('email-available', '0');
            }
            else if (d == 'yes')
            {
                $(obj).next('span').html('该邮箱可用').css({'color':'green', 'font-size':'12px'});
                // 同上
                $(obj).attr('email-available', '1');
            }
        });
    }
}