layui.config({
    base: "js/"
}).use(['form', 'layer'], function () {
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : parent.layer,
        $ = layui.jquery;
    //登录按钮事件
    form.on("submit(login)", function (data) {
        var datas = "username=" + data.field.username + "&password=" + data.field.password + "&captcha=" + data.field.captcha;
        $.ajax({
            type: "POST",
            url: "/admin/login/login_username",
            data: datas,
            //dataType: "json",
            success: function (result) {
                if (result.code == 200) {//登录成功
                    layer.msg(result.msg, {icon: 1, time: 1000}, function(){
                        parent.location.href = result.url;
                    });
                } else {
                    layer.msg(result.msg, {icon: 5});
                    refreshCode();
                }
            }
        });
        return false;
    })
});
function refreshCode(){
    var captcha = document.getElementById("captcha");
    captcha.src = '/captcha.html?'+Math.random();
}
