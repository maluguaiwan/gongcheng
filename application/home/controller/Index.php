<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use app\common\model\Banner;
use app\common\model\News;
use app\common\model\Category;
use app\common\model\ProductTag;
use app\common\model\Product;
use app\common\model\DoctorInfoView;

class Index extends IndexBase
{
    public function index()
    {
    	/* banner */
    	$banner = Banner::order('sort','asc')->limit(5)->select();
    	$this->assign('banner',$banner);
    	
    	/* news */
    	$news = News::order('create_time','DESC')->where('cat_id=1')->limit(5)->select();
    	$this->assign('news',$news);
    	
    	/* 医生分类 */
    	$doctorCategory = Category::where(['type'=>2])->order('sort','ASC')->limit(7)->select();
    	$this->assign('doctor_category',$doctorCategory);
    	
    	/* 药品分类 */
    	$medicineCategory = Category::where(['type'=>1,'status'=>1])->order('sort','ASC')->limit(7)->select();
    	$this->assign('medicine_category',$medicineCategory);
    	
    	/* 标签 及标签下的药品  */
    	// TODO
    	$tags = ProductTag::where(['is_show'=>1])->order('sort','ASC')->select();
    	if($tags){
    		foreach ($tags as $key => $tag){
    			$products = Product::where("FIND_IN_SET('".$tag['id']."',`tag`) and status=1")->order('sort','ASC')->limit(4)->select();
    			$tags[$key]['products'] = $products;
    		}
    	}
    	$this->assign('tags',$tags);

    	//推荐医生
        $model=new DoctorInfoView();
        $doctor_rec_list=$model->alias('A')
            ->field('A.*,B.name category_name')
            ->join('Category B','A.category_id=B.id','left')
            ->where('A.if_rec=1')
            ->select();
        $this->assign('doctor_rec_list',$doctor_rec_list);
    	return $this->fetch();
    }

}
