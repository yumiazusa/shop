$(function () {
    //弹出添加新地址的弹框
    $(".btn-add-addr").click(function () {
        $(".pop-default").css("display", "block");

    });

    //关闭新地址的弹框
    $(".pop-close").click(function () {
        $(".pop-default").css("display", "none");
    });

    //验证收货人
    $("input[name=receiver]").blur(function () {
        //alert("aaaa");
        checkReciver();
    });
    //验证详细地址
    $("input[name=detail]").blur(function () {
        checkDetail();
    });
    //验证手机号
    $("input[name=mobile]").blur(function () {
        checkMobile();
    });
    //保存新添加的收货地址
    $("#save-addr").click(function () {

        //获取收货地址的id,如果没有就是添加新的地址，否则是修改地址
        var _addId = $("#a-addrId");
        if (_addId.val() == "") {
            addressSave();
            saveAddress();
        } else {
            editAddress(_addId.val());
        }
    });
    //基本信息和上传头像之间的切换
    infor();
    //修改邮箱时验证
    $("#email").blur(function () {
        email();
    });

    $(".send-email").click(function () {
        var isEmail = email();
        if (isEmail) {
            return true;
        } else {
            return false;
        }
    });
    //修改密码
    $("#spwd1").focus(function () {
        $(this).hide();
        $("#oldpassword").css("display", "block");
    });
    $("#oldpassword").blur(function () {
        checkPwd();
    });
    $("#spwd2").focus(function () {
        $(this).hide();
        $("#newpassword").css("display", "block");
    });
    $("#newpassword").blur(function () {
        checkNewPwd();
    });
    $("#spwd3").focus(function () {
        $(this).hide();
        $("#qrpassword").css("display", "block");
    });
    $("#qrpassword").blur(function () {
        checkQrPwd();
    });
    //投诉弹框显示
    $("#suggest").click(function () {
        $(".complaint").show();
    });
    $("#thickclose").click(function () {
        $(".complaint").hide();
    })
});

//验证收货人
function checkReciver() {
    var ischeckReciver = false;
    var textcolor = "red";
    var msg = "";
    var _reciver = $("#receiver");
    if (_reciver.val() == "") {
        msg = "请填写收货人";

    } else {
        ischeckReciver = true;
    }
    //alert(msg);
    _reciver.next().html(msg);
    return ischeckReciver;

}
//验证详细地址
function checkDetail() {
    var ischeckDetail = false;
    var textcolor = "red";
    var msg = "";
    var _detail = $("#detail");
    if (_detail.val() == "") {
        msg = "请填写详细的收货地址";

    } else {
        ischeckDetail = true;
    }
    //alert(msg);
    _detail.next().html(msg);
    return ischeckDetail;

}
//验证手机号
function checkMobile() {
    var ischeckMobile = false;
    var textcolor = "red";
    var msg = "";
    var _mobile = $("#mobile");
    var re = /^1[3|4|5|8][0-9]\d{4,8}$/;
    if (_mobile.val() == "") {
        msg = "请填写手机号码";
    } else if (_mobile.val().match(re) == null) {
        msg = "请填写正确的手机号码";

    } else {
        ischeckMobile = true;
    }
    //alert(msg);
    _mobile.next().html(msg);
    return ischeckMobile;

}

//保存新添的地址
function saveAddress() {
    var isReciver = checkReciver();
    var isDetail = checkDetail();
    var isMobild = checkMobile();
    var str = "";
    if (isReciver && isDetail && isMobild) {
        $(".pop-default").css("display", "none");
        var receiver = $("#receiver").val();
        var province = $("#province").val();
        var city = $("#city").val();
        var district = $("#district").val();
        var street = $("#street").val();
        var detail = $("#detail").val();
        var mobile = $("#mobile").val();
        var idDefault = $("#isDefault").val();
        var addrNum = $(".addr-num").html();
        str = '<div class="box-addr uc-box-addr" id="addr">';
        str += '<input type="hidden" name="a-addrId" class="radio a-addrId">';
        str += '<header>';
        str += '<div class="box-addr-title">';
        str += '<span class="a-alias"></span>';
        str += '<a class="f-blue a-del" href="">删除</a><a class="f-blue a-edit" href="">编辑</a>';
        str += '<a class="f-blue a-setDefault   hide-this" href="">设为默认</a>';
        str += '<span class="a-isDefault default-addr ">默认地址</span>';
        str += '</div></header>';
        str += '<ul class="box-addr-cont">';
        str += '<li>收&nbsp;&nbsp;货&nbsp;&nbsp;人：<span class="a-receiver">' + receiver + '</span></  li>';
        str += '<li>地&#12288;&#12288;址：';
        str += '<span class="a-province">' + province + '</span>';
        str += '<span class="a-city">' + city + '</span>';
        str += '<span class="a-district">' + district + '</span>';
        str += '<span class="a-street">' + street + '</span>';
        str += '<span class="a-detail">' + detail + '</span>';
        str += '</li>';
        str += '<li>联系电话：<span class="a-mobile">' + mobile + '</span>';
        str += '<span class="a-tel"></span>';
        str += '</li></ul></div>';
        $("#list-addr").append(str);
        addrNum++;
        $(".addr-num").html(addrNum);
        $(".addr-num").css("color", "red");

    } else {
        return false;
    }
}

//删除地址
function del() {
    var _obj = $("#addr");
    if (confirm("确认要删除吗?")) {
        _obj.slideUp();
    }


}
//修改地址时弹框并带数据
function edit(obj) {
    $("#a-addrId").val(obj);
    var receiver = $("#revi-1").html();
    var moblie = $("#mob-1").html();
    var detail = $("#det-1").html();
    $("input[name=receiver]").val(receiver);
    $("input[name=detail]").val(detail);
    $("input[name=mobile]").val(moblie);
    $(".pop-default").css("display", "block");

}
//修改地址
function editAddress(obj) {
    var _id = obj;
    var _reciver = "#revi-" + _id;
    var _detail = "#det-" + _id;
    var _mobile = "#mob-" + _id;
    var _editReceiver = $("input[name=receiver]").val();
    var _editDetail = $("input[name=detail]").val();
    var _editMobile = $("input[name=mobile]").val();
    var isReciver = checkReciver();
    var isDetail = checkDetail();
    var isMobild = checkMobile();
    if (isReciver && isDetail && isMobild) {
        $(_reciver).html(_editReceiver);
        $(_detail).html(_editDetail);
        $(_mobile).html(_editMobile);
        $(".pop-default").css("display", "none");
    } else {
        return false;
    }

}

//基本信息之间的切换
function infor() {
    $(".info-msg-list li").click(function () {
        var _this = $(this);
        if (!_this.hasClass("list")) {
            _this.siblings().removeClass("list");
            _this.addClass("list");
            $(".list-ul").children("li").removeClass("activing").eq(_this.index()).addClass("activing");
        }
    });
}
//修改邮箱时验证
function email() {
    alert(1111);
    var isEmail = false;
    var reg = new RegExp(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/);
    var msg = "";
    var email = $("input[name=email]");
    if (reg.test(email.value)) {
        textColor = "green";
        isEmail = true;

    } else {
        msg = "邮箱格式不正确";
    }
    email.next().html(msg);
    return isEmail;
}
function checkPwd() {
    var isPwd = false;
    var _oldPwd = $("#oldpassword");

    var msg = "";
    if (_oldPwd.val() == "") {
        msg = "请输入密码";
        _oldPwd.next().show();
    } else {
        isPwd = true;
        _oldPwd.next().css("display", "none");
    }

    _oldPwd.next().html(msg);
    return isPwd;

}
function checkNewPwd() {
    var isNewPwd = false;
    var _newPwd = $("#newpassword");

    var msg = "";
    if (_newPwd.val() == "") {
        msg = "请输入新密码";
        _newPwd.next().show();
    } else if (_newPwd.val().length < 6 || _newPwd.val().length > 18) {
        msg = "密码长度在6~18位";
        _newPwd.next().show();

    } else {
        isPwd = true;
        _newPwd.next().css("display", "none");
    }

    _newPwd.next().html(msg);
    return isNewPwd;

}
function checkQrPwd() {
    var isQrPwd = false;
    var newPwd = $("#newpassword").val();
    var _qrPwd = $("#qrpassword");
    var msg = "";
    if (_qrPwd.val() == "") {
        msg = "请输入确认密码";
        _qrPwd.next().show();
    } else if (_qrPwd.val() != newPwd) {
        msg = "确认密码与新密码不一致";
        _qrPwd.next().show();

    } else {
        isPwd = true;
        _qrPwd.next().css("display", "none");
    }

    _qrPwd.next().html(msg);
    return isQrPwd;

}
function dosubmit() {
    var isPwd = checkPwd();
    var isNewPwd = checkNewPwd();
    var isQrPwd = checkQrPwd();
    if (isPwd && isNewPwd && isQrPwd) {
        return true;
    } else {
        return false;
    }
}
function applyNum(obj) {
    var num = parseInt($("input[name=applyNum]").val());
    if (obj == 1) {

        if (num > 1) {
            $("input[name=applyNum]").val(num - 1);
        }
    } else if (obj == 2) {
        $("input[name=applyNum]").val(num + 1);
    }
}

//ajax提交表单
/*
function addressSave() {
    $(function () {


        if (dosubmit()) {
            $.ajax({

                type: "POST",
                url: "__CONTROLLER__/addressAdd",
                data: {
                    'reciver_user':$('#receiver').val(),
                    'province':$('#provice').val(),
                    'city':$('#city').val(),
                    'district':$('#area').val(),
                    'street':$('#street').val(),
                    'reciver_phone':$('#mobile').val(),
                    'is_default':$('#isDefault').checked()?1:0
                },

                success: function (msg) {
                    alert("新地址添加成功!"+msg);
                },
                error:function(msg){
                    alert(msg);
                }
            })
        }
    })
}*/

