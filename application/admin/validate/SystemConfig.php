<?php
namespace app\admin\validate;

class SystemConfig extends ValidateBase
{
    /**
     * 校验规则
     * @var array
     */
    protected $rule = [
        'group'  => 'require|alphaDash',
        'vari'  => 'require|alphaDash',
        'value'  => 'chsDash',
        'type'  => 'require',
    ];

    /**
     * 校验NG返回错误信息
     * @var array
     */
    protected $message= [
        'group.require' => '分组名称不能为空',
        'group.alphaDash' => '分组名称必选为字母和数字，下划线_及破折号-',
        'value.chsDash' => '数据不合法',
        'vari.require' => '变量名称不能为空',
        'vari.alphaDash' => '变量名称必选为字母和数字，下划线_及破折号-',
        'type.require' => '类型不能为空',
    ];
}