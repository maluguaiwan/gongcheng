<?php
namespace app\admin\controller;

use app\admin\validate\AuthRule as AuthRuleValidate;
use app\common\controller\AdminBase;
use app\common\model\AuthRule as AuthRuleModel;
use think\Request;

/** 权限控制器（包括后台菜单控制）
 * Class AuthRule
 * @package app\admin\controller
 */
class AuthRule extends AdminBase
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '权限列表',
                'description' => '管理网站所有权限',
            ),
            'menu' => array(
                array(
                    'name' => '权限列表',
                    'url' => url('index')
                )
            ),
            '_info' => array(
                array(
                    'name' => '添加新规则',
                    'url' => url('add'),
                )
            )
        );
    }

    /** 输出权限/菜单列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(){
        $model = new AuthRuleModel();
        $menu = $model->order('orders asc,id asc')->select();
        $menus = $model->menuList($menu);
        $this->assign('menus',$menus);
        return $this->fetch();
    }

    /** 添加权限节点
     * @param Request $request
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add(Request $request){
        if ($request->isPost()){
            $model = new AuthRuleModel();
            //是提交操作
            $post = $this->request->post();
            //验证部分数据合法性
            $is_Check=(new AuthRuleValidate())->goCheck($post);
            if($is_Check !==true){
                return ajaxReturn(0,'提交失败');
            }
            $post['name']=$post['module'].'/'.$post['controller'].'/'.$post['function'];
            //验证菜单是否存在
            $menu = $model->where(['name'=>$post['name']])->find();
            if(!empty($menu)) {
                return ajaxReturn(0,'该规则已经存在');
            }

            if(false == $model->allowField(true)->save($post)) {
                return ajaxReturn(0,'添加权限失败');
            } else {
                //记录日志
                $this->add_log($this->userSession['id'],$this->userSession['username'],'添加权限规则：'.$post['name']);
                return ajaxReturn(200,'添加权限成功',url('index'));
            }
        }else{
            //非提交操作
            $pid = $this->request->has('pid') ? $this->request->param('pid', null, 'intval') : $pid;
            $model = new AuthRuleModel();
            if($pid>0){
                $menu = $model->where('id',$pid)->find();
                if(empty($menu)) {
                    return ajaxReturn(0,'pid不正确');
                }
                $rule_name=explode("/",$menu['name']);
                $menu['title']='';
                $menu['id']='';
                $menu['pid']='';
                $menu['module']=$rule_name[0];
                $menu['controller']=$rule_name[1];
                $this->assign('menu',$menu);
                $this->assign('pid',$pid);
            }
            $menu = $model->select();
            $menus = $model->menuList($menu);
            $this->assign('menus',$menus);
            return $this->fetch('info');
        }
    }

    /** 修改权限节点
     * @param Request $request
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit(Request $request){
        if ($request->isPost()){
            $model = new AuthRuleModel();
            //是提交操作
            $post = $this->request->post();

            //验证部分数据合法性
            $is_Check=(new AuthRuleValidate())->goCheck($post);
            if($is_Check !==true){
                return ajaxReturn(0,'提交失败'. $is_Check);
            }
            $post['name']=$post['module'].'/'.$post['controller'].'/'.$post['function'];
            //验证菜单是否存在
//            $menu = $model->where(['title'=>$post['title'],['id','neq',$post['id']]])->find();
            $menu = $model->where('title="'.$post['title'].'" and id != "'.$post['id'].'"')->find();
            if(!empty($menu)) {
                return ajaxReturn(0,'该规则标题已经存在');
            }
//            $menu = $model->where(['name'=>$post['name'],['id','neq',$post['id']]])->find();
            $menu = $model->where('name="'.$post['name'].'" and  id != "'.$post['id'].'"')->find();
            if(!empty($menu)) {
                return ajaxReturn(0,'该规则方法已经存在');
            }
            if(false == $model->allowField(true)->save($post,['id'=>$post['id']])) {
                return ajaxReturn(0,'修改失败');
            } else {
                //记录日志
                $this->add_log($this->userSession['id'],$this->userSession['username'],'修改权限规则ID:'.$post['id']);
                return ajaxReturn(200,'修改权限信息成功',url('index'));
            }
        }else{
            //获取菜单id
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') :$id;
            if($id == 0) {
                return ajaxReturn(0,'id不正能为空');
            }
            $model = new AuthRuleModel();
            //非提交操作
            $menu = $model->where('id',$id)->find();
            if(empty($menu)) {
                //$this->error('id不正确');
                return ajaxReturn(0,'id不正确');
            }
            $menus = $model->select();
            $menus_all = $model->menuList($menus);
            $this->assign('menus',$menus_all);

            $rule_name=explode("/",$menu['name']);
            $menu['module']=$rule_name[0];
            $menu['controller']=$rule_name[1];
            $menu['function']=$rule_name[2];
            $this->assign('menu',$menu);
            return $this->fetch('info');
        }

    }

    /** 删除权限节点
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete()
    {
        if($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            $where['pid']=$id;
            if((new AuthRuleModel())->where($where)->select()->isEmpty()) {
                if(false ==(new AuthRuleModel())->where('id',$id)->delete()) {
                    $this->error('删除失败');
                } else {
                    //记录日志
                    $this->add_log($this->userSession['id'],$this->userSession['username'],'删除权限规则ID:'.$id);
                    $this->success('删除成功','admin/auth_rule/index');
                }
            } else {
                $this->error('该菜单下还有子菜单，不能删除');
            }
        }
    }

    /**
     * 更新排序
     */
    public function orders()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            $i = 0;
            foreach ($post['id'] as $k => $val) {
                $order = (new AuthRuleModel())->where('id',$val)->value('orders');
                if($order != $post['orders'][$k]) {
                    if(false == (new AuthRuleModel())->where('id',$val)->update(['orders'=>$post['orders'][$k]])) {
                        $this->error('更新失败');
                    } else {
                        $i++;
                    }
                }
            }
            $this->success('成功更新'.$i.'个数据','admin/auth_rule/index');
        }
    }
}