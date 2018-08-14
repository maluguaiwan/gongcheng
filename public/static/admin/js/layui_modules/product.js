/**
 项目JS主入口
 以依赖layui的layer和form模块为例
 **/
layui.define(['layer', 'form','upload','element', 'webuploader','laydate'], function(exports){
    var layer = layui.layer,
        form = layui.form,
        $ = layui.jquery,
        upload = layui.upload,
        element = layui.element,
        WebUploader = layui.webuploader(),
        laydate = layui.laydate;

    /***********************************单模块开始***********************************/
    //日期
    $('.date').each(function(){
        var format=$(this).attr('date_format');
        var date_type=$(this).attr('date_type');
        if (format==undefined){
            format='yyyy-MM-dd';
        }
        if (date_type==undefined){
            date_type='date';
        }
        laydate.render({
            elem: this
            ,position: 'fixed'
            ,type:date_type
            ,format: format
        });
    })
    /***********************************单模块结束***********************************/
    /***********************************表单提交开始***********************************/
    //列表内页表单
    form.on('submit(info_submit)', function(data){
        // console.log(data.elem) //被执行事件的元素DOM对象，一般为button对象
        // console.log(data.form) //被执行提交的form对象，一般在存在form标签时才会返回
        // console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
        var url = data.form.getAttribute('action');
        var data = data.field;
        $.ajax({
            url:url,
            data:data,
            type:'post',
            async: false,
            success:function(res) {
                if(res.code == 200) {
                    layer.open({
                        type: 1
                        ,title: false //不显示标题栏
                        ,closeBtn: false
                        ,area: '300px;'
                        ,shade: 0.8
                        ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                        ,btn: ['返回列表', '继续操作']
                        ,moveType: 1 //拖拽模式，0或者1
                        ,content: '<div style="padding: 20px;text-align: center; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;font-size: 16px">'+res.msg+'</div>'
                        ,success: function(layero){
                            var btn = layero.find('.layui-layer-btn');
                            btn.css('text-align', 'center');
                            btn.find('.layui-layer-btn0').attr({
                                href: res.url
                            });
                            btn.find('.layui-layer-btn1').attr({
                                href: ''
                            });
                        }
                    });
                } else {
                    layer.msg(res.msg, {icon: 5,time:2000});
                }
            }
        })
        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    });
    /***********************************表单提交结束***********************************/
    /***********************************上传操作开始***********************************/
    //单图上传
    var uploadOne = upload.render({
            elem: '.upload_one' //绑定元素
            ,url: "/admin/Upload/upload_image" //上传接口
            ,accept: 'images' //允许上传的文件类型可选值有：images（图片）、file（所有文件）、video（视频）、audio（音频）
            ,field:'file'//设定文件域的字段名
            ,multiple:false //是否允许多文件上传。设置 true即可开启。不支持ie8/9
            ,number:0 //设置同时可上传的文件数量，一般配合 multiple 参数出现。注意：该参数为 layui 2.2.3 开始新增
            ,drag:true //是否接受拖拽的文件上传，设置 false 可禁用。不支持ie8/9
            ,before: function(obj){
            //开启特效
            loading = layer.load(2, {
                shade: [0.2,'#000']
            });
            //layer.load(); //上传loading
            //element.progress('demo', '70%');
            //预读本地文件示例，不支持ie8
            // obj.preview(function(index, file, result){
                //$('#demo1').attr('src', result); //图片链接（base64）
            // });
        }
        ,done: function(res){
            var item = this.item;
            var hidden_input=item.next().next();
            var img_item=item.nextAll('.layui-upload-list').children('img');
            //上传完毕回调
            layer.close(loading);//关闭特效
            if(res.code == 200) {
                hidden_input.attr({'value':res.url});
                img_item.attr('src',res.url);
            } else {
                layer.msg(res.msg);
            }
        }
        ,error: function(){
            //请求异常回调
            //演示失败状态，并实现重传
            layer.close(loading);//关闭特效
            var demoText = $('#demoText');
            demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
            demoText.find('.demo-reload').on('click', function(){
                uploadOne.upload();
            });
        }
    });
    /*-------------------------webupload开始----------------------------*/
    if($('#uploader').length>0){
        //定义上传组件
        $wrap = $('#uploader'),

            // 图片容器
            $queue = $('<ul class="filelist"></ul>')
                .appendTo($wrap.find('.queueList')),

            // 状态栏，包括进度和控制按钮
            $statusBar = $wrap.find('.statusBar'),

            // 文件总体选择信息。
            $info = $statusBar.find('.info'),

            // 上传按钮
            $upload = $wrap.find('.uploadBtn'),

            // 没选择文件之前的内容。
            $placeHolder = $wrap.find('.placeholder'),

            // 总体进度条
            $progress = $statusBar.find('.progress').hide(),

            // 添加的文件数量
            fileCount = 0,

            // 添加的文件总大小
            fileSize = 0,

            // 优化retina, 在retina下这个值是2
            ratio = window.devicePixelRatio || 1,

            // 缩略图大小
            thumbnailWidth = 110 * ratio,
            thumbnailHeight = 110 * ratio,

            // 可能有pedding, ready, uploading, confirm, done.
            state = 'pedding',

            // 所有文件的进度信息，key为file id
            percentages = {},

            supportTransition = (function() {
                var s = document.createElement('p').style,
                    r = 'transition' in s ||
                        'WebkitTransition' in s ||
                        'MozTransition' in s ||
                        'msTransition' in s ||
                        'OTransition' in s;
                s = null;
                return r;
            })(),

            // WebUploader实例
            uploader;

        if (!WebUploader.Uploader.support()) {
            layer.msg('Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
            throw new Error('WebUploader does not support the browser you are using.');
        }

        // 实例化
        uploader = WebUploader.create({
            pick: {
                id: '#filePicker',
                label: '点击选择图片'
            },
            dnd: '#uploader .queueList',
            paste: document.body,

            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            },


            disableGlobalDnd: true,

            chunked: true,
            // server: 'http://webuploader.duapp.com/server/fileupload.php',
            server: '/admin/Upload/upload_image',
            fileNumLimit: 300,
            fileSizeLimit: 5 * 1024 * 1024, // 200 M
            fileSingleSizeLimit: 5 * 1024 * 1024 // 50 M
        });

        // 添加“添加文件”的按钮，
        uploader.addButton({
            id: '#filePicker2',
            label: '继续添加'
        });

        // 当有文件添加进来时执行，负责view的创建
        function addFile(file) {
            var $li = $('<li id="' + file.id + '">' +
                '<p class="title">' + file.name + '</p>' +
                '<p class="imgWrap"></p>' +
                '<p class="progress"><span></span></p>' +
                '</li>'),

                $btns = $('<div class="file-panel">' +
                    '<span class="cancel">删除</span>' +
                    '<span class="rotateRight">向右旋转</span>' +
                    '<span class="rotateLeft">向左旋转</span></div>').appendTo($li),
                $prgress = $li.find('p.progress span'),
                $wrap = $li.find('p.imgWrap'),
                $info = $('<p class="error"></p>'),

                showError = function(code) {
                    switch (code) {
                        case 'exceed_size':
                            text = '文件大小超出';
                            break;

                        case 'interrupt':
                            text = '上传暂停';
                            break;

                        default:
                            text = '上传失败，请重试';
                            break;
                    }

                    $info.text(text).appendTo($li);
                };

            if (file.getStatus() === 'invalid') {
                showError(file.statusText);
            } else {
                // @todo lazyload
                $wrap.text('预览中');
                uploader.makeThumb(file, function(error, src) {
                    if (error) {
                        $wrap.text('不能预览');
                        return;
                    }

                    var img = $('<img src="' + src + '">');
                    $wrap.empty().append(img);
                }, thumbnailWidth, thumbnailHeight);

                percentages[file.id] = [file.size, 0];
                file.rotation = 0;
            }

            file.on('statuschange', function(cur, prev) {
                if (prev === 'progress') {
                    $prgress.hide().width(0);
                } else if (prev === 'queued') {
                    $li.off('mouseenter mouseleave');
                    $btns.remove();
                }

                // 成功
                if (cur === 'error' || cur === 'invalid') {
                    console.log(file.statusText);
                    showError(file.statusText);
                    percentages[file.id][1] = 1;
                } else if (cur === 'interrupt') {
                    showError('interrupt');
                } else if (cur === 'queued') {
                    percentages[file.id][1] = 0;
                } else if (cur === 'progress') {
                    $info.remove();
                    $prgress.css('display', 'block');
                } else if (cur === 'complete') {
                    $li.append('<span class="success"></span>');
                }

                $li.removeClass('state-' + prev).addClass('state-' + cur);
            });

            $li.on('mouseenter', function() {
                $btns.stop().animate({ height: 30 });
            });

            $li.on('mouseleave', function() {
                $btns.stop().animate({ height: 0 });
            });

            $btns.on('click', 'span', function() {
                var index = $(this).index(),
                    deg;

                switch (index) {
                    case 0:
                        uploader.removeFile(file);
                        return;

                    case 1:
                        file.rotation += 90;
                        break;

                    case 2:
                        file.rotation -= 90;
                        break;
                }

                if (supportTransition) {
                    deg = 'rotate(' + file.rotation + 'deg)';
                    $wrap.css({
                        '-webkit-transform': deg,
                        '-mos-transform': deg,
                        '-o-transform': deg,
                        'transform': deg
                    });
                } else {
                    $wrap.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');
                    // use jquery animate to rotation
                    // $({
                    //     rotation: rotation
                    // }).animate({
                    //     rotation: file.rotation
                    // }, {
                    //     easing: 'linear',
                    //     step: function( now ) {
                    //         now = now * Math.PI / 180;

                    //         var cos = Math.cos( now ),
                    //             sin = Math.sin( now );

                    //         $wrap.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
                    //     }
                    // });
                }


            });

            $li.appendTo($queue);
        }

        // 负责view的销毁
        function removeFile(file) {
            var $li = $('#' + file.id);

            delete percentages[file.id];
            updateTotalProgress();
            $li.off().find('.file-panel').off().end().remove();
        }

        function updateTotalProgress() {
            var loaded = 0,
                total = 0,
                spans = $progress.children(),
                percent;

            $.each(percentages, function(k, v) {
                total += v[0];
                loaded += v[0] * v[1];
            });

            percent = total ? loaded / total : 0;

            spans.eq(0).text(Math.round(percent * 100) + '%');
            spans.eq(1).css('width', Math.round(percent * 100) + '%');
            updateStatus();
        }

        function updateStatus() {
            var text = '',
                stats;

            if (state === 'ready') {
                text = '选中' + fileCount + '张图片，共' +
                    WebUploader.formatSize(fileSize) + '。';
            } else if (state === 'confirm') {
                stats = uploader.getStats();
                if (stats.uploadFailNum) {
                    text = '已成功上传' + stats.successNum + '张照片至XX相册，' +
                        stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
                }

            } else {
                stats = uploader.getStats();
                text = '共' + fileCount + '张（' +
                    WebUploader.formatSize(fileSize) +
                    '），已上传' + stats.successNum + '张';

                if (stats.uploadFailNum) {
                    text += '，失败' + stats.uploadFailNum + '张';
                }
            }

            $info.html(text);
        }

        function setState(val) {
            var file, stats;

            if (val === state) {
                return;
            }

            $upload.removeClass('state-' + state);
            $upload.addClass('state-' + val);
            state = val;

            switch (state) {
                case 'pedding':
                    $placeHolder.removeClass('element-invisible');
                    $queue.parent().removeClass('filled');
                    $queue.hide();
                    $statusBar.addClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'ready':
                    $placeHolder.addClass('element-invisible');
                    $('#filePicker2').removeClass('element-invisible');
                    $queue.parent().addClass('filled');
                    $queue.show();
                    $statusBar.removeClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'uploading':
                    $('#filePicker2').addClass('element-invisible');
                    $progress.show();
                    $upload.text('暂停上传');
                    break;

                case 'paused':
                    $progress.show();
                    $upload.text('继续上传');
                    break;

                case 'confirm':
                    $progress.hide();
                    $upload.text('开始上传').addClass('disabled');

                    stats = uploader.getStats();
                    if (stats.successNum && !stats.uploadFailNum) {
                        setState('finish');
                        return;
                    }
                    break;
                case 'finish':
                    stats = uploader.getStats();
                    if (stats.successNum) {
                        layer.msg('上传成功');
                    } else {
                        // 没有成功的图片，重设
                        state = 'done';
                        location.reload();
                    }
                    break;
            }

            updateStatus();
        }

        uploader.onUploadProgress = function(file, percentage) {
            var $li = $('#' + file.id),
                $percent = $li.find('.progress span');

            $percent.css('width', percentage * 100 + '%');
            percentages[file.id][1] = percentage;
            updateTotalProgress();
        };

        uploader.onFileQueued = function(file) {
            fileCount++;
            fileSize += file.size;

            if (fileCount === 1) {
                $placeHolder.addClass('element-invisible');
                $statusBar.show();
            }

            addFile(file);
            setState('ready');
            updateTotalProgress();
        };

        uploader.onFileDequeued = function(file) {
            fileCount--;
            fileSize -= file.size;

            if (!fileCount) {
                setState('pedding');
            }

            removeFile(file);
            updateTotalProgress();

        };

        uploader.on('all', function(type) {
            var stats;
            switch (type) {
                case 'uploadFinished':
                    setState('confirm');
                    break;

                case 'startUpload':
                    setState('uploading');
                    break;

                case 'stopUpload':
                    setState('paused');
                    break;

            }
        });

        uploader.onError = function(code) {
            layer.msg('Eroor: ' + code);
        };

        $upload.on('click', function() {
            if ($(this).hasClass('disabled')) {
                return false;
            }

            if (state === 'ready') {
                uploader.upload();
            } else if (state === 'paused') {
                uploader.upload();
            } else if (state === 'uploading') {
                uploader.stop();
            }
        });

        $info.on('click', '.retry', function() {
            uploader.retry();
        });

        $info.on('click', '.ignore', function() {
            layer.msg('todo');
        });

        $upload.addClass('state-' + state);
        updateTotalProgress();

        //上传后，服务端返回的信息
        var i = $('#webupload_hidden_input input').length;
        uploader.on('uploadSuccess', function(file, ret) {
            console.log(ret);
            html_img_show='<div style="float: left">' +
                '<img src="'+ret.url+'" width="150px" class="img-thumbnail">' +

                '<div style="margin: 10px;">' +
                '<input type="text" name="pics['+i+'][sort]" placeholder="排序" style="width: 80px;" value="10">' +
                '</div>' +
                '<div style="text-align: center;cursor: pointer;" onclick="webupload_img_del(this)" webupload_img_del_id="'+i+'" class="webupload_img_del">删除</div>' +
                '</div>';
            html='<input type="hidden" name="pics['+i+'][url]" value="'+ret.url+'">';
            $('#webupload_hidden_img_show').append(html_img_show);
            $('#webupload_hidden_input').append(html);
            i++;
        });
        window.webupload_img_del=function(obj) {
            if(i!=0){
                key_id=$(obj).attr('webupload_img_del_id');
                $(obj).parent().remove();
                $("input[name='pics["+key_id+"][url]']").remove();
            }
        }
    }
    /*-------------------------webupload结束----------------------------*/
    /***********************************上传操作结束***********************************/
    exports('product', {}); //注意，这里是模块输出的核心，模块名必须和use时的模块名一致
}); 