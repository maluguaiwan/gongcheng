<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\DoctorInfoView;
use app\common\model\MemberLevel as MemberLevelModel;
use app\common\model\Member as MemberModel;
use app\common\model\DoctorInfo as DoctorInfoModel;
use think\Request;

/**
 * 医生会员管理
 */

class DoctorReview extends AdminBase {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '医生会员管理',
                'description' => '管理网站调用医生会员',
            ),
            'menu' => array(
                array(
                    'name' => '医生会员列表',
                    'url' => url('index'),
                    'icon' => 'list',
                )
            ),
            /*'_info' => array(
                array(
                    'name' => '添加医生会员',
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
        $where='C.status!=1';
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
        $model=new DoctorInfoModel();
        $list=$model->alias('A')
            ->field('A.*,B.name category_name,C.parent_id')
            ->join('Category B','A.category_id=B.id','left')
            ->join('Member C','A.id=C.id')
            ->where($where)
            ->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 医生详情
     */
    public function detail(Request $request){
        $doctor_id=$request->param('doctor_id');
        if (empty($doctor_id)){
            return $this->error('参数错误');
        }
        $model=new DoctorInfoView();
        $info=$model->alias('A')
            ->field('A.*,B.name category_name')
            ->join('Category B','A.category_id=B.id')
            ->where('A.id="'.$doctor_id.'"')
            ->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    /*
     * 审核通过
     */
    public function agree(Request $request){
        $id=$request->param('id');
        $parent_id=$request->param('parent_id');
        if (empty($id)){
            return ajaxReturn(0,'参数不足');
        }
        $model=new MemberModel();
        $status=$model->where('id="'.$id.'"')->setField('status',1);
        if($status!==false){
            if ($parent_id>0){
                reward(3,$id);
            }
            return ajaxReturn(200,'操作成功',url('index'));
        }else{
            return ajaxReturn(0,'操作失败');
        }
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
            $status=model('Member')->add();
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
        $model = model('Member');
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
     * 删除
     */
    public function del(Request $request){
        $id=$request->param('id');
        if(empty($id)){
            return ajaxReturn(0,'参数不能为空');
        }
        if(model('Member')->del($id)){
            return ajaxReturn(200,'医生会员删除成功！');
        }else{
            return ajaxReturn(0,'医生会员删除失败！');
        }
    }
}

