<?php
namespace app\admin\validate;

class AdminUser extends ValidateBase
{
    /**
     * 校验规则
     * @var array
     */
    protected $rule = [
        'id'         =>'number',
        'username'  => 'alphaDash|max:25',
        'nickname'  => 'chsAlphaNum|max:25',
        'password'  => 'alphaNum|confirm',
        'email'=>'email',
        'phone'=>'mobile'
    ];

    /**
     * 校验NG返回错误信息
     * @var array
     */
    protected $message= [

    ];
}