<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Request;

/**
 * 医生分类管理
 */

class DoctorCategory extends AdminBase {
    protected $type=2;
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '医生分类管理',
                'description' => '管理网站调用医生分类',
            ),
            'menu' => array(
                array(
                    'name' => '医生分类列表',
                    'url' => url('index'),
                    'icon' => 'list',
                )
            ),
            '_info' => array(
                array(
                    'name' => '添加医生分类',
                    'url' => url('add'),
                )
            )
        );
    }
    /**
     * 列表
     */
    public function index(){
        //筛选条件
        $where = array();
        $where[]=['type','eq',$this->type];
        $where[]=['is_delete','neq',1];
        $keyword = input('keyword');
        $status = input('status');
        if(!empty($keyword)){
            $where[] = ['ykb_input_text','like','%'.$keyword.'%'];
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
        $list=model('Category')->loadList($where);
        //模板传值
        $this->assign('list',$list);
        $this->assign('count',count($list));
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
            $status=model('Category')->add();
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $where[]=['type','eq',$this->type];
            $where[]=['parent_id','eq',0];
            $where[]=['is_delete','neq',1];
            $category_list=model('Category')->loadList($where);
            $this->assign('category_list',$category_list);
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
        $model = model('Category');
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
            $where[]=['type','eq',$this->type];
            $where[]=['parent_id','eq',0];
            $where[]=['is_delete','neq',1];
            $category_list=model('Category')->loadList($where);
            $this->assign('category_list',$category_list);
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
        if (model('Category')->getWhereInfo('parent_id="'.$id.'" and is_delete != 1')){
            return ajaxReturn(0,'请先删除子分类');
        }
        if(model('Category')->del($id)){
            return ajaxReturn(200,'医生分类删除成功！');
        }else{
            return ajaxReturn(0,'医生分类删除失败！');
        }
    }
}

