<?php
namespace app\common\model;
use think\exception\DbException;
use think\Model;

/**
 * Class Shili
 * @package app\common\model
 */
class CouponCategory extends Model {
    protected $autoWriteTimestamp = true;
    protected $pk = 'id';
//    protected $createTime = 'add_time';
    /*
     * 列表
     */
    public function loadList($type='all', $where=array(), $field='*', $limit=15, $order='sort ASC,id DESC'){
        $object = self::where($where)->field($field)->order($order);
        if ($type=='all'){
            $object=$object->limit($limit)->select();
        }else{
            $object=$object->paginate($limit);
        }
        return $object;
    }

    /** 主键id获取信息
     * @param $id
     * @return array
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInfo($id)
    {
        $map = array();
        $map[]=[$this->pk,'=',$id];
        return $this->getWhereInfo($map);
    }

    /** Where条件获取信息
     * @param array $where
     * @param string $field
     * @return array
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getWhereInfo($where=array(),$field='*')
    {
        $info=self::field($field)->where($where)->find();
        return $info ? $info->toArray() : array();
    }

    /** 新增
     * @param array $data
     * @return static
     */
    public function add($data=array())
    {
        if (empty($data)){
            $data=$_POST;
        }
        return self::create($data,true);
    }

    /** 修改
     * @param array $data
     * @param array $where
     * @return static
     */
    public function edit($data=array(),$where=array())
    {
        if (empty($data)){
            $data=$_POST;
        }
        $where[]=[$this->pk,'=',$_POST[$this->pk]];
        return self::update($data,$where,true);
    }

    /** 主键id删除
     * @param int $id ID
     * @return bool 删除状态
     */
    public function del($id)
    {
        $map[] = [$this->pk,'=',$id];
        return self::where($map)->delete();
    }

}
