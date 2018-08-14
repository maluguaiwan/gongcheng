<?php
namespace app\admin\validate;

class AuthGroup extends ValidateBase
{
    /**
     * 校验规则
     * @var array
     */
    protected $rule = [
        'title'  => 'require|max:25',
        'rules'  => 'array',
    ];

    /**
     * 校验NG返回错误信息
     * @var array
     */
    protected $message= [
        'title.require'  => '权限名称不能为空',
        'title.max'       => '权限名称最多不能超过25个字符',
        'rules.array'     => '规则格式不正确',
    ];
}