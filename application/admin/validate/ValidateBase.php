<?php
namespace app\admin\validate;
use think\Validate;

/** 基础验证类
 * Class ValidateBase
 * @package app\admin\validate
 */
class ValidateBase extends Validate
{
    /** 重构验证方法
     * @param $params
     * @return array|bool
     */
    public function goCheck($params)
    {
        if($this->check($params)){
            return true;
        }
        return $this->error;
    }
    protected function isNoChinese($str){
        if (preg_match("/[\x7f-\xff]/", $str)) {
            return false;
        }else{
           return true;
        }
    }
}