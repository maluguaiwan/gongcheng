<?php
namespace app\admin\validate;


/** 规则/菜单 提交数据校验
 * Class AuthRule
 * @package app\admin\validate
 */
class AuthRule extends ValidateBase
{

    /**
     * 校验规则
     * @var array
     */
    protected $rule = [
        'title'  => 'require|max:25',
        'module'  => 'isNoChinese',
        'controller'  => 'isNoChinese',
        'function'  => 'isNoChinese',
        'pid'   => 'require',
        'status'   => 'require',
        'type'   => 'require',
    ];

    /**
     * 校验NG返回错误信息
     * @var array
     */
    protected $message= [
        'title.require' => '权限名称不能为空',
        'controller.isNoChinese' => '控制器名不能为中文',
        'module.isNoChinese' => '模块名不能为中文',
        'function.isNoChinese' => '方法名不能为中文',
        'title.max'     => '权限名称最多不能超过25个字符',
        'pid.require'   => '请选择上级菜单',
        'status.require'   => '请选择状态',
        'type.require'   => '请选择权限类型',
    ];
}