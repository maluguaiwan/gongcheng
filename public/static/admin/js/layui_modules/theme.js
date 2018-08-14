/**
 项目JS主入口
 以依赖layui的layer和form模块为例
 **/
layui.define(['layer', 'form'], function(exports){
    var layer = layui.layer,
        form = layui.form,
        jq = layui.jquery;

        var status=0;
        form.on('switch(switchStatus)', function(data){
            var loading = layer.load(2, {
                shade: [0.2,'#000']
            });
            if(data.elem.checked){
                status=1;
            }else{
                status=0;
            }
            var url="/admin/Theme/update_status?id="+data.value+'&status='+status ;

            jq.get(url,function(data){

                if(data.code == 200){
                    layer.close(loading);
                    layer.msg(data.msg, {icon: 1, time: 500}, function(){
                        location.reload();
                    });
                }else{
                    layer.close(loading);
                    layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
                }
            });
            return false;
        });
        form.on('switch(switchInstall)', function(data){
            loading = layer.load(2, {
                shade: [0.2,'#000']
            });
            if(data.elem.checked){
                status=1;
            }else{
                status=0;
            }
            var url="/admin/Theme/update_install?"+data.value+'&status='+status ;

            jq.get(url,function(data){

                if(data.code == 200){
                    layer.close(loading);
                    layer.msg(data.msg, {icon: 1, time: 500}, function(){
                        location.reload();
                    });
                }else{
                    layer.close(loading);
                    layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
                }
            });
            return false;
        });
        $('.delete').click(function(){
            var id = $(this).attr('id');
            layer.confirm('确定要删除?', function(index) {
                $.ajax({
                    url:"/admin/Theme/delete_theme",
                    data:{id:id},
                    success:function(res) {
                        layer.msg(res.msg);
                        if(res.code == 1) {
                            setTimeout(function(){
                                location.href = res.url;
                            },1500)
                        }
                    }
                })
            })
        })
    exports('theme', {}); //注意，这里是模块输出的核心，模块名必须和use时的模块名一致
}); 