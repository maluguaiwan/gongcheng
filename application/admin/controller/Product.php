<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Request;

/**
 * 药品管理
 */

class Product extends AdminBase {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '药品管理',
                'description' => '管理网站调用药品',
            ),
            'menu' => array(
                array(
                    'name' => '药品列表',
                    'url' => url('index'),
                    'icon' => 'list',
                )
            ),
            '_info' => array(
                array(
                    'name' => '添加药品',
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
        $where[]=['A.is_delete','neq',1];
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
        $list=model('Product')->loadList('page',$where,'A.*,B.name',15,'A.sort ASC,A.id DESC');
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
            if (!empty($_POST['tag'])){
                $_POST['tag']=implode(',',$_POST['tag']);
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
            $status=model('Product')->add();
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            //药品分类查询
            $where[]=['type','eq',1];
            $cat_list=model('Category')->loadList($where);
            //产品标签
            $tag_list=model('ProductTag')->loadList('all','','*',15,'sort ASC');
            $this->assign('tag_list',$tag_list);
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
        $model = model('Product');
        if (empty($id)){
            return $this->error('参数错误!');
        }
        if ($request->isPost()){
            /******************数据处理开始（可选择添加处理）*********************/
            //checkbox 以逗号分隔存入数据库
            if (!empty($_POST['tag'])){
                $_POST['tag']=implode(',',$_POST['tag']);
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
            //药品分类查询
            $where[]=['type','eq',1];
            $cat_list=model('Category')->loadList($where);
            $this->assign('cat_list',$cat_list);
            //产品标签
            $tag_list=model('ProductTag')->loadList('all','','*',15,'sort ASC');
            $this->assign('tag_list',$tag_list);
            //获取信息
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
        if(model('Product')->del($id)){
            return ajaxReturn(200,'药品删除成功！');
        }else{
            return ajaxReturn(0,'药品删除失败！');
        }
    }
}

