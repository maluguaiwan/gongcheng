<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Request;

/**
 * 文章管理
 */

class News extends AdminBase {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '文章管理',
                'description' => '管理网站调用文章',
            ),
            'menu' => array(
                array(
                    'name' => '文章列表',
                    'url' => url('index'),
                    'icon' => 'list',
                )
            ),
            '_info' => array(
                array(
                    'name' => '添加文章',
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
        $keyword = input('keyword');
        $status = input('status');
        $cat_id = input('cat_id');
        if(!empty($keyword)){
            $where[] = ['title','like','%'.$keyword.'%'];
        }
        if(!empty($cat_id)){
            $where[] = ['cat_id','eq',$cat_id];
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
        $list=model('News')->loadList('page',$where);
        //模板传值
        $this->assign('list',$list);
        $this->assign('cat_list',model('NewsCategory')->loadList('page',array(),'*',15,'sort ASC'));
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
            $status=model('News')->add();
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            //文章分类查询
            $cat_list=model('NewsCategory')->loadList('all');
            $this->assign('cat_list',$cat_list);
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
        $model = model('News');
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
            //文章分类查询
            $cat_list=model('NewsCategory')->loadList('all');
            $this->assign('cat_list',$cat_list);
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
        if(model('News')->del($id)){
            return ajaxReturn(200,'文章删除成功！');
        }else{
            return ajaxReturn(0,'文章删除失败！');
        }
    }
}

