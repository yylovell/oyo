<?php

namespace app\admin\model;

use think\exception\PDOException;
use think\Model;

class Node extends Model
{

    protected $table = "oyo_node";

    /**
     * 获取节点数据
     */
    public function getNodeInfo($id)
    {
        $result = $this->field('id,node_name,typeid')->select();
        $str = "";

        $role = new UserType();
        $rule = $role->getRuleById($id);

        if(!empty($rule)){
            $rule = explode(',', $rule);
        }
        foreach($result as $key=>$vo){
            $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['typeid'] . '", "name":"' . $vo['node_name'].'"';

            if(!empty($rule) && in_array($vo['id'], $rule)){
                $str .= ' ,"checked":1';
            }

            $str .= '},';

        }

        return "[" . substr($str, 0, -1) . "]";
    }

    /**
     * 根据节点数据获取对应的菜单
     * @param $nodeStr
     * @return array
     */
    public function getMenu($nodeStr = '')
    {
        //超级管理员没有节点数组
        $where = empty($nodeStr) ? 'is_menu = 2' : 'is_menu = 2 and id in('.$nodeStr.')';

        $result = db('node')->field('id,node_name,typeid,control_name,action_name,style')
            ->where($where)->select();
        $menu = prepareMenu($result);

        return $menu;
    }

    /**
     * 根据案例id获取信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getById($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 获取数据
     * @return mixed
     */
    public function getList()
    {
        return $this->field('id,node_name name,typeid pid,is_menu,style,control_name,action_name')->select();
    }

    /**
     * 插入信息
     * @param $param
     * @return array
     */
    public function insert($param)
    {
        try{

            $this->save($param);
            return msg(1, $this->id, '添加成功');
        }catch(PDOException $e){

            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 编辑
     * @param $param
     * @return array
     */
    public function edit($param)
    {
        try{

            $this->save($param, ['id' => $param['id']]);
            return msg(1, '', '编辑成功');
        }catch(PDOException $e){

            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 删除
     * @param $id
     * @return array
     */
    public function del($id)
    {
        try{

            $this->where('id', $id)->delete();
            return msg(1, '', '删除成功');

        }catch(PDOException $e){
            return msg(-1, '', $e->getMessage());
        }
    }
}