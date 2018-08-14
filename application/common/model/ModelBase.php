<?php
namespace app\common\model;
use think\Model;

class ModelBase extends Model
{
    /**
     * 开启自动记录时间戳
     * @var bool
     */
    protected  $autoWriteTimestamp = true;
}