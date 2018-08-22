<?php

namespace app\common\model;

use app\common\controller\Sys;
use think\exception\PDOException;
use think\Model;

class Vipstudent extends Model
{
    protected $table = 'oyo_vip_student';

    public function students()
    {
        return $this->belongsTo('Students', 'student_id', 'id');
    }

    public function grade()
    {
        return $this->belongsTo('Coursegrade', 'grade_id', 'id');
    }

    public function learncourse()
    {
        return $this->belongsTo('learncourse', 'learn_id', 'id');
    }

    /**
     * 根据搜索条件获列表信息
     * @param $where
     * @param $order
     * @param $offset
     * @param $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getByWhere($where, $order, $offset, $limit)
    {
        return $this->with(['students', 'grade', 'learncourse'])->where($where)->order($order)->limit($offset, $limit)->select();
    }

    /**
     * 获取今日会员课信息
     * @param int $pageNumber
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getTodayData($pageNumber = 1, $pageSize = 10)
    {
        $day = date('Y-m-d', time());

        $Min = $day . ' ' . '00:00:00';
        $Max = $day . ' ' . '23:59:59';

        $where = ['start_at' =>['>=', $Min], 'end_at' => ['<=', $Max]];
        $order = 'start_at';

        $limit = $pageSize;
        $offset = ($pageNumber - 1) * $limit;

        $aData = $this->getByWhere($where, $order, $offset, $limit);

        $aDataTime = [];
        foreach ($aData as $item)
        {
            $day = substr($item['start_at'], 0, 10);
            $start_at = substr($item['start_at'], 11, 5);
            $end_at = substr($item['end_at'], 11, 5);

            $aDataTime[$day . ' ' . $start_at . ' 至 '. $end_at][] = $item;
        }

        $aDataGrade = [];
        foreach ($aDataTime as $k=>$v)
        {
            foreach ($v as $item)
            {
                $aDataGrade[$k][$item['grade_id']][] = $item;
            }
        }

        return $aDataGrade;
    }

    /**
     * 根据搜索条件获取数量
     * @param $where
     * @return int
     */
    public function getAll($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 插入信息
     * @param $param
     * @return array
     */
    public function insert($param)
    {
        try {
            $result = $this->save($param);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }
            else {
                return ['code' => 1, 'data' => $this->id, 'msg' => '报名成功, 详情请查看个人中心'];
            }
        } catch (PDOException $e) {

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }




    /**
     * 编辑信息
     * @param $param
     * @return array
     */
    public function edit($param)
    {
        try {

            $result = $this->validate('VipStudentValidate')->save($param, ['id' => $param['id']]);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 修改课程等级信息
     * @param $param
     * @return array
     */
    public function editGrade($param)
    {
        try {

            $result = $this->save($param, ['id' => $param['id']]);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据id获取信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getById($id)
    {
        return $this->with(['students', 'grade', 'learncourse'])->find($id);
    }


    /**
     * 删除
     * @param $id
     * @return array
     */
    public function del($id)
    {
        try {
            $this->where('id', $id)->delete();

            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];

        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}