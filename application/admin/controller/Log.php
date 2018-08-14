<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\Log as LogModel;

class Log extends AdminBase
{
    /** 登录
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function login(){
        $model=new LogModel();
        $logLogin=$model->where(['controller'=>'login','is_delete'=>0])->paginate(20);
        $this->assign('logLogin',$logLogin);
        return $this->fetch();
    }

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function admin(){
        $model=new LogModel();
        $logAdmin=$model->where(['module'=>'admin','is_delete'=>0])->paginate(20);
        $this->assign('logAdmin',$logAdmin);
        return $this->fetch();
    }

    /**
     *
     */
    public function delete(){
        if($this->request->isAjax()) {
            $post = $this->request->param();
            $is_delete = (new LogModel())->where('id',$post['id'])->value('is_delete');
            if($is_delete == 0) {
                if(true == (new LogModel())->where('id',$post['id'])->update(['is_delete'=>1])) {
                    $this->success('删除成功');
                }
            }
            $this->error('删除失败');
        }
    }

}