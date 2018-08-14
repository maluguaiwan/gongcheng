/**
 项目JS主入口
 以依赖layui的layer和form模块为例
 **/
layui.define(['layer', 'form','upload'], function(exports){
    var layer = layui.layer,
        form = layui.form,
        $ = layui.jquery;

    //layer.msg('Hello World');

    /***********************************监听事件开始***********************************/
    //switch 事件监听
    form.on('switch(switchStatus)', function(data){
        var obj=data.elem;
        var loading = layer.load(2, {
            shade: [0.2,'#000']
        });
        if(obj.checked){ status=1; }else{ status=0; }
        var url='/admin/admin_api/upfield';
        var table=obj.getAttribute('table');//表名
        var id_name=obj.getAttribute('id_name');//条件字段
        var id_value=obj.getAttribute('id_value');//条件值
        var field=obj.getAttribute('field');//修改的字段
        var field_value=status;//修改的值
        $.get(url,{table:table,id_name:id_name,id_value:id_value,field:field,field_value:field_value},function(data){
            if(data.code == 200){
                layer.close(loading);
                layer.msg(data.msg, {icon: 1, time: 1000}, function(){
                    //  location.reload();
                });
            }else{
                layer.close(loading);
                layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
            }
        });
        return false;
    });
    //radio 事件监听
    form.on('radio(radioStatus)', function(data){
        var obj=data.elem;
        var loading = layer.load(2, {
            shade: [0.2,'#000']
        });
        var url='/admin/admin_api/upfield';
        var table=obj.getAttribute('table');//表名
        var id_name=obj.getAttribute('id_name');//条件字段
        var id_value=obj.getAttribute('id_value');//条件值
        var field=obj.getAttribute('field');//修改的字段
        var field_value=obj.value;//修改的值
        $.get(url,{table:table,id_name:id_name,id_value:id_value,field:field,field_value:field_value},function(data){
            if(data.code == 200){
                layer.close(loading);
                layer.msg(data.msg, {icon: 1, time: 1000}, function(){
                    //  location.reload();
                });
            }else{
                layer.close(loading);
                layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
            }
        });
        return false;
    });
    //checkbox 事件监听
    form.on('checkbox(checkboxStatus)', function(data){
        var obj=data.elem;
        var checkbox_name=obj.getAttribute('name');
        var value = $("input:checkbox[name="+checkbox_name+"]:checked").map(function (index, elem) {
            return $(elem).val();
        }).get().join(',');
        var loading = layer.load(2, {
            shade: [0.2,'#000']
        });
        if(obj.checked){ status=1; }else{ status=2; }
        var url='/admin/admin_api/upfield';
        var table=obj.getAttribute('table');//表名
        var id_name=obj.getAttribute('id_name');//条件字段
        var id_value=obj.getAttribute('id_value');//条件值
        var field=obj.getAttribute('field');//修改的字段
        var field_value=value;//修改的值
        $.get(url,{table:table,id_name:id_name,id_value:id_value,field:field,field_value:field_value},function(data){
            if(data.code == 200){
                layer.close(loading);
                layer.msg(data.msg, {icon: 1, time: 1000}, function(){
                    //  location.reload();
                });
            }else{
                layer.close(loading);
                layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
            }
        });
        return false;
    });

    //input text 监听
    $('.list_text').change(function () {
        var obj=$(this);
        loading = layer.load(2, {
            shade: [0.2,'#000']
        });
        var url='/admin/admin_api/upfield';
        var table=obj.attr('table');//表名
        var id_name=obj.attr('id_name');//条件字段
        var id_value=obj.attr('id_value');//条件值
        var field=obj.attr('field');//修改的字段
        var field_value=obj.val();//修改的值
        $.get(url,{table:table,id_name:id_name,id_value:id_value,field:field,field_value:field_value},function(data){
            if(data.code == 200){
                layer.close(loading);
                layer.tips(data.msg, obj, {
                    tips: [1, '#66CD00'],
                    time: 2000
                });
            }else{
                layer.close(loading);
                layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
            }
        });
        return false;
    })
    //单个删除事件
    $('.delete').click(function(){
        var obj=$(this);
        var id = obj.attr('id');
        var url = obj.attr('url');
        layer.confirm('确定要删除?', function(index) {
            $.ajax({
                url:url,
                data:{id:id},
                success:function(res) {
                    layer.msg(res.msg);
                    if(res.code == 200) {
                        setTimeout(function(){
                            //location.href = res.url;
                            obj.parent().parent().remove();
                        },1500)
                    }
                }
            })
        })
    });
    form.on('submit(config_orders)', function(data) {
        var url ="/admin/system_config/orders";
        $.ajax({
            url:url,
            data:$('#config_form').serialize(),
            type:'post',
            async: false,
            success:function(res) {
                if(res.code == 1) {
                    layer.alert(res.msg, function(index){
                        location.href = res.url;
                    })
                } else {
                    layer.msg(res.msg);
                }
            }
        });
        return false;
    });
    /***********************************监听事件结束***********************************/
    /***********************************全选操作事件开始***********************************/
    //更改状态事件
    $('.enable').click(function(){
        var field=$(this).attr('field');
        var field_value=$(this).attr('field_value');
        var url='/admin/admin_api/upfieldall/field/'+field+'/field_value/'+field_value;
        var data=$('#admin').serialize();
        $.ajax({
            url:url,
            data:data,
            type:'post',
            async: false,
            success:function(res) {
                if(res.code == 200) {
                    layer.msg(res.msg, {icon: 1, time: 1000},function(index){
                        window.location.reload();
                    })
                } else {
                    layer.msg(res.msg);
                }
            }
        })
    });
    //软删除事件
    $('.delete_all').click(function(){
        var url='/admin/admin_api/delfieldall';
        var data=$('#admin').serialize();
        layer.confirm('确定要删除选中项吗?', function(index) {
            $.ajax({
                url:url,
                data:data,
                type:'post',
                async: false,
                success:function(res) {
                    if(res.code == 200) {
                        layer.msg(res.msg, {icon: 1, time: 1000},function(index){
                            window.location.reload();
                        })
                    } else {
                        layer.msg(res.msg);
                    }
                }
            })
        })
    });
    //硬删除事件
    $('.delete_all_hard').click(function(){
        var url='/admin/admin_api/delfieldallhard';
        var data=$('#admin').serialize();
        layer.confirm('确定要删除选中项吗?', function(index) {
            $.ajax({
                url:url,
                data:data,
                type:'post',
                async: false,
                success:function(res) {
                    if(res.code == 200) {
                        layer.msg(res.msg, {icon: 1, time: 1000},function(index){
                            window.location.reload();
                        })
                    } else {
                        layer.msg(res.msg);
                    }
                }
            })
        })
    });
    /***********************************全选操作事件结束***********************************/
    exports('list', {}); //注意，这里是模块输出的核心，模块名必须和use时的模块名一致
}); 