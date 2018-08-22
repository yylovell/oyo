<?php

namespace app\common\model;

use think\exception\PDOException;
use think\Model;

class Footer extends Model
{

    /**
     * @var string 表名
     */
    protected $table = 'oyo_footer';

    public function updateInfo($param)
    {
        try {
            $result = $this->validate('ConfigValidate')->save($param, ['id' => 1]);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '更新成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


}