<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Db;
use think\Request;

/**
 * 后台api
 * +----------------------------------------------------------------------
 */
class AdminApi extends AdminBase
{
    /**
     * 后台初始化
     */
    protected function initialize()
    {
        parent::initialize();
    }

    /** 更新单个字段
     * @param Request $request
     * @return \think\response\Json
     */
    public function upField(Request $request){
        $table=$request->param('table');//表名
        $id_name=$request->param('id_name');//条件字段
        $id_value=$request->param('id_value');//条件值
        $field=$request->param('field');//修改的字段
        $field_value=$request->param('field_value');//修改的值
        if ($field_value=='false'){
            $field_value=0;
        }
        if (empty($table)||empty($id_name)||empty($id_value)||empty($field)||$field_value===false){
            return ajaxReturn(0,'参数不足');
        }
        $where[]=[$id_name,'eq',$id_value];
        $status=Db::name($table)->where($where)->setField($field,$field_value);
        if ($status){
            return ajaxReturn(200,'操作成功');
        }else{
            return ajaxReturn(0,'操作失败');
        }
    }

    /** 更新所选字段
     * @param Request $request
     * @return \think\response\Json
     */
    public function upFieldAll(Request $request){
        $table=$request->param('table');//表名
        $id_name=$request->param('id_name');//条件字段
        $data_array=$request->param($id_name.'/a');//数据
        $field=$request->param('field');//修改的字段
        $field_value=$request->param('field_value');//修改的值
        $where[]=[$id_name,'in',implode(',',$data_array)];
        $status=Db::name($table)->where($where)->setField($field,$field_value);
        if ($status){
            return ajaxReturn(200,'批量操作成功');
        }else{
            return ajaxReturn(0,'批量操作失败');
        }
    }

    /** 删除所选字段
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delFieldall(Request $request){
        $table=$request->param('table');//表名
        $id_name=$request->param('id_name');//条件字段
        $data_array=$request->param($id_name.'/a');//数据
        $where[]=[$id_name,'in',implode(',',$data_array)];
        $status=Db::name($table)->where($where)->setField('is_delete','1');
        if ($status){
            return ajaxReturn(200,'批量删除成功');
        }else{
            return ajaxReturn(0,'批量删除失败');
        }
    }
    /** 删除所选字段
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delFieldallHard(Request $request){
        $table=$request->param('table');//表名
        $id_name=$request->param('id_name');//条件字段
        $data_array=$request->param($id_name.'/a');//数据
        $where[]=[$id_name,'in',implode(',',$data_array)];
        $status=Db::name($table)->where($where)->delete();
        if ($status){
            return ajaxReturn(200,'批量删除成功');
        }else{
            return ajaxReturn(0,'批量删除失败');
        }
    }

}