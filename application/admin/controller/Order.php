<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Request;

/**
 * 订单管理
 */

class Order extends AdminBase {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '订单管理',
                'description' => '管理网站调用订单',
            ),
            'menu' => array(
                array(
                    'name' => '订单列表',
                    'url' => url('index'),
                    'icon' => 'list',
                )
            ),
            '_info' => array(
                /*array(
                    'name' => '添加订单',
                    'url' => url('add'),
                )*/
            )
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
        $list=model('Order')->loadList('page',$where,'A.*,B.name member_name,B.nickname,B.mobile member_mobile',20);
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
            if (!empty($_POST['ykb_date'])){
                $_POST['ykb_date']=strtotime($_POST['ykb_date']);
            }
            /******************数据处理结束*********************/
            $status=model('Order')->add();
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
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
        $model = model('Order');
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
            if (!empty($_POST['ykb_date'])){
                $_POST['ykb_date']=strtotime($_POST['ykb_date']);
            }
            /******************数据处理结束*********************/
            $status=$model->edit();
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $info=$model->getInfo($id);
            $this->assign('info',$info);
            return $this->fetch('info');
        }
    }
    /**
     * 订单详情
     */
    public function detail(Request $request){
        if ($request->isPost()){
            $_POST['courier_time']=time();
            $status=model('Order')->edit();
            if($status!==false){
                return ajaxReturn(200,'操作成功');
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $order_id=$request->param('id');
            if (empty($order_id)){
                return $this->error('参数错误');
            }
            //订单信息
            $order_info=model('Order')->getInfo($order_id);
            $info['order_info']=$order_info;
            //订单商品信息
            $order_product_list=model('OrderProducts')->loadList('all','A.order_id="'.$order_id.'"');
            $info['order_product_list']=$order_product_list;
            //优惠券信息
            $coupon_info=model('Coupon')->getInfo($order_info['coupon']);
            $info['coupon_info']=$coupon_info;
            $this->assign('info',$info);
            return $this->fetch();
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
        if(model('Order')->del($id)){
            return ajaxReturn(200,'订单删除成功！');
        }else{
            return ajaxReturn(0,'订单删除失败！');
        }
    }
}

