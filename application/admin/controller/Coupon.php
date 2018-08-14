<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\News as NewsModel;
use think\Request;

/**
 * 优惠券管理
 */

class Coupon extends AdminBase {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '优惠券管理',
                'description' => '管理网站调用优惠券',
            ),
            'menu' => array(
                array(
                    'name' => '优惠券列表',
                    'url' => url('index'),
                    'icon' => 'list',
                )
            ),
            /*'_info' => array(
                array(
                    'name' => '添加优惠券',
                    'url' => url('add'),
                )
            )*/
        );
    }
    /**
     * 列表
     */
    public function index(){
        //筛选条件
        $where = array();
        $keyword = input('keyword');
        $status = input('status');
        $where[]=['is_delete','neq',1];
        if(!empty($keyword)){
            $where['ykb_input_text'] = ['like','%'.$keyword.'%'];
        }
        if($status!==null){
            switch ($status) {
                case '1':
                    $where['status'] = 1;
                    break;
                case '2':
                    $where['status'] = 2;
                    break;
            }
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['status'] = $status;
        //查询数据
        $list=model('Coupon')->loadList('page',$where,'A.*,B.name',15,'id ASC');
        //模板传值
        $this->assign('list',$list);
        $this->assign('count',$list->total());
        $this->assign('pageMaps',$pageMaps);
        return $this->fetch();
    }

    /**
     * 添加
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request){
        if ($request->isPost()){
            /******************数据处理开始（可选择添加处理）*********************/
            //checkbox 以逗号分隔存入数据库
            if (!empty($_POST['ykb_input_checkbox'])){
                $_POST['ykb_input_checkbox']=implode(',',$_POST['ykb_input_checkbox']);
            }
            //多图路径以json格式存入数据库
            if (!empty($_POST['pics'])){
                $_POST['pics']=json_encode($_POST['pics']);
            }
            //时间处理
            if (!empty($_POST['validity_time'])){
                $_POST['validity_time']=strtotime($_POST['validity_time']);
            }
            /******************数据处理结束*********************/
            $status=model('Coupon')->add();
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $coupon_category=model('CouponCategory')->loadList('all',array(),'*',15,'sort ASC');
            $this->assign('coupon_category',$coupon_category);
            return $this->fetch('info');
        }
    }

    /**
     * 修改
     * @param Request $request
     * @return mixed|\think\response\Json|void
     */
    public function edit(Request $request){
        $id=$request->param('id');
        $model = model('Coupon');
        if (empty($id)){
            return $this->error('参数错误!');
        }
        if ($request->isPost()){
            /******************数据处理开始（可选择添加处理）*********************/
            //checkbox 以逗号分隔存入数据库
            if (!empty($_POST['ykb_input_checkbox'])){
                $_POST['ykb_input_checkbox']=implode(',',$_POST['ykb_input_checkbox']);
            }
            //多图路径以json格式存入数据库
            if (!empty($_POST['pics'])){
                $_POST['pics']=json_encode($_POST['pics']);
            }

            //时间处理
            if (!empty($_POST['validity_time'])){
                $_POST['validity_time']=strtotime($_POST['validity_time']);
            }
            /******************数据处理结束*********************/
            $status=$model->edit();
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $coupon_category=model('CouponCategory')->loadList('all',array(),'*',15,'sort ASC');
            $this->assign('coupon_category',$coupon_category);
            $info=$model->getInfo($id);
            $this->assign('info',$info);
            return $this->fetch('info');
        }
    }
    /**
     * 删除
     */
    public function del(Request $request){
        $id=$request->param('id');
        if(empty($id)){
            return ajaxReturn(0,'参数不能为空');
        }
        //删除操作
        if(model('Coupon')->del($id)){
            return ajaxReturn(200,'优惠券删除成功！');
        }else{
            return ajaxReturn(0,'优惠券删除失败！');
        }
    }
}

