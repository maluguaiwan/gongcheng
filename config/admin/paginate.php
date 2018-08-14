<?php
//分页配置
return [
    'type'      => '\app\common\paginator\Layui',
    'var_page'  => 'page',
    'list_rows' => 15,
    'query'     => \think\facade\Request::param('')
];