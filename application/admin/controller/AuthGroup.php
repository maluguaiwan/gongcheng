<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\AuthGroup as AuthGroupModel;
use app\admin\validate\AuthGroup as AuthGroupValidate;
use app\common\model\AuthGroupAccess;
use app\common\model\AuthRule as AuthRuleModel;
use think\Request;

/** 权限组（角色）控制器
 * Class AuthGroup
 * @package app\admin\controller
 */
class AuthGroup extends AdminBase
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '角色列表',
                'description' => '管理网站角色列表',
            ),
            'menu' => array(
                array(
                    'name' => '角色列表',
                    'url' => url('index'),
                    'icon' => 'list',
                )
            ),
            '_info' => array(
                array(
                    'name' => '添加角色',
                    'url' => url('add'),
                )
            )
        );
    }
    /** 渲染输入角色类列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(){
        //实例化管理员模型
        $model = new AuthGroupModel();
        $role_list = $model->paginate(20);
        $this->assign('role_list',$role_list);
        return $this->fetch();
    }

    /** 添加角色类信息
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request){
        if ($request->isPost()){
            $model = new AuthGroupModel();
            //是提交操作
            $post = $this->request->post();
            //验证部分数据合法性
            $is_Check=(new AuthGroupValidate())->goCheck($post);
            if($is_Check !==true){
                return ajaxReturn(0,'提交失败：' . $is_Check);
            }
            if(false == $model->allowField(true)->save($post)) {
                return ajaxReturn(0,'添加角色失败');
            } else {
                $this->add_log($this->userSession['id'],$this->userSession['username'],'添加角色：'.$post['title']);
                return ajaxReturn(200,'添加角色成功',url('index'));
            }
        }else{
            return $this->fetch('info');
        }
    }

    /** 编辑角色类信息
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit(Request $request){
        if ($request->isPost()){
            //是修改操作
            $model = new AuthGroupModel();
            //是提交操作
            $post = $this->request->post();
            //验证部分数据合法性
            $is_Check=(new AuthGroupValidate())->goCheck($post);
            if($is_Check !==true){
                return ajaxReturn(0,'提交失败：' . $is_Check);
            }
            //验证菜单是否存在
            $role = $model->where(['title'=>$post['title'],['id','neq',$post['id']]])->find();
            if(!empty($role)) {
                return ajaxReturn(0,'该角色标题已经存在');
            }
            if(false == $model->allowField(true)->save($post,['id'=>$post['id']])) {
                return ajaxReturn(0,'修改失败');
            } else {
                $this->add_log($this->userSession['id'],$this->userSession['username'],'修改角色ID:'.$post['id'].'角色信息');
                return ajaxReturn(200,'修改权限信息成功',url('admin/auth_group/index'));
            }
        }else{
            //获取菜单id
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') :$id;
            if($id == 0) {
                return $this->error('id不能为空');
            }
            $model = new AuthGroupModel();
            //非提交操作
            $role = $model->where('id',$id)->find();
            if(empty($role)) {
                return $this->error('id不正确');
            }
            $this->assign('role',$role);
            return $this->fetch('info');
        }

    }

    /** 编辑角色类授权规则
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit_rule($id=0){
        //获取角色id
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') :$id;
        $model = new AuthGroupModel();
        //是修改操作
        if($id == 0) {
            $this->error('id不能为空');
        }
        //非提交操作
        $role = $model->find(['id'=>$id]);
        $role['rules']=explode(",", $role['rules']);
        $model = new AuthRuleModel();
        if ($id>1){
            $where[]=['id','not in','1,2,3,7'];
        }
        $menu = $model->where($where)->order('orders asc')->select();
        $menus = $model->menuList($menu);
//        var_dump($menus);exit;
        $this->assign('menus',$menus);
        $this->assign('role',$role);
        return $this->fetch();
    }

    /**
     * 修改规则动作
     */
    public function update_rule(){
        //是修改操作
        if($this->request->isPost()) {
            $model = new AuthGroupModel();
            //是提交操作
            $post = $this->request->post();
            $save['rules'] = implode(",", $post['rules']);
            if (false == $model->allowField(true)->save($save, ['id' => $post['id']])) {
                $this->error('修改规则失败');
            } else {
                $this->add_log($this->userSession['id'],$this->userSession['username'],'修改角色ID:'.$post['id'].'规则');
                $this->success('修改规则信息成功','admin/auth_group/index');
            }
        }else{
            $this->error('修改规则失败:非法提交！');
        }
    }

    /** 更新角色类状态 开启|禁止
     * @return \think\response\Json
     */
    public function update_status(){
        if ($this->request->isGet()) {
            $get = $this->request->get();
            $model = new AuthGroupModel();
            if ($model->where('id', $get['id'])->update(['status' =>$get['status']]) !== false) {
                $this->add_log($this->userSession['id'],$this->userSession['username'],'更新角色ID：'.$get['id'].'状态');
                //  $this->success('更新成功');
                return json(array('code' => 200, 'msg' => '更新成功'));
            }
        }
        // $this->error('更新失败');
        return json(array('code' => 0, 'msg' => '更新失败'));
    }

    /**
     * @return \think\response\Json
     */
    public function del(){
        $id=$this->request->param('id');
        if(empty($id)){
            return ajaxReturn(0,'参数不能为空');
        }
        $auth_group_model=new AuthGroupModel();
        $auth_group_access_model = new AuthGroupAccess();
        if($auth_group_model->del($id)){
            $auth_group_access_model->where(array('auth_group_id'=>$id))->delete();
            return ajaxReturn(200,'角色删除成功！');
        }else{
            return ajaxReturn(0,'角色删除失败！');
        }
    }
}