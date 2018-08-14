<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use app\common\model\Banner;
use app\common\model\News;
use app\common\model\Category;
use app\common\model\ProductTag;
use app\common\model\Product;

class TestPay extends IndexBase
{
    public function test()
    {
    	return $this->fetch('test');
    }

}
