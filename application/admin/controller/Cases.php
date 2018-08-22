<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 案例控制类
 *
 * Class Cases
 * @package app\admin\controller
 */
class Cases extends Base
{

    public $_cModel;

    /**
     * 案例分类模型类
     * @var
     */
    public $_type;

    public function _initialize()
    {
        parent::_initialize();
        $this->_cModel = new \app\admin\model\Cases();

        if (in_array($this->request->action(), ['lists', 'casesadd', 'casesedit'])) {
            $ccModel = new \app\common\model\Casecat();

            $aData = $ccModel->getCaseCat();
            $type_name_map = [];
            foreach ($aData as $v){
                $type_name_map[$v['id']] = $v;
            }
            $this->_type = $type_name_map;

        }
    }

    /**
     * 案例列表
     * @return mixed
     */
    public function lists()
    {

        if (request()->isAjax()) {

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];
            if (isset($param['searchText'])) {
                if ($param['searchText']['title']) {
                    $where['title'] = ['like', '%' . $param['searchText']['title'] . '%'];
                }
                if ((int)$param['searchText']['type'] !== 1000) {
                    $where['type'] = $param['searchText']['type'];
                }
                if ((int)$param['searchText']['is_classical'] !== 1000) {
                    $where['is_classical'] = $param['searchText']['is_classical'];
                }
            }
            $selectResult = $this->_cModel->getCasesByWhere($where, $offset, $limit);
            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['time'] = substr($selectResult[$key]['time'], 0, 10);
                $selectResult[$key]['is_classical'] = onButton('cases/status', Sys::getIsClassical($selectResult[$key]['is_classical']), $selectResult[$key]['is_classical'], "javascript:status('" . $vo['id'] . "' , '" . $selectResult[$key]['is_classical'] . "')");

                // $operate = ['编辑' => url('cases/casesEdit', ['id' => $vo['id']]), '删除' => "javascript:casesDel('" . $vo['id'] . "')"];
                $operate = [
                    '编辑' => [
                        'href' => url('cases/casesEdit', ['id' => $vo['id']]),
                        'auth' => 'cases/casesedit'
                    ],
                    '删除' => [
                        'href' => "javascript:casesDel('" . $vo['id'] . "')",
                        'auth' => 'cases/casesdel'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_cModel->getAllCases($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }
        $is_classical_map = Sys::getIsClassical();

        $this->assign(['is_classical_map' => $is_classical_map, 'type_name_map' => $this->_type]);
        return $this->fetch();
    }

    //添加案例
    public function casesAdd()
    {
        //
        try {
            if (request()->isPost()) {
                $param = input();

                if ($param['is_classical'] == Sys::CASES_IS_CLASSICAL) {
                    $res = $this->_cModel->classicalCount($param['type']);
                    if ($res > 2) {
                        throw new Exception('首页显示该类型案例已经超过三个');
                    }
                }

                $thumb = $this->_getThumb($param['photo']);


                $arr = ['title' => $param['title'], 'customer' => $param['customer'], 'product' => $param['product'], 'platform' => $param['platform'], 'actors' => $param['actors'], 'rating' => $param['rating'], 'web_rating' => $param['web_rating'], 'prize' => $param['prize'], 'is_classical' => $param['is_classical'], 'rank' => $param['rank'], 'time' => $param['time'], 'type' => $param['type'], 'content' => $param['content'], 'photo' => $param['photo'], 'photo_thumb' => $thumb, 'photo1' => $param['photo1'], 'photo2' => $param['photo2'], 'photo3' => $param['photo3'], 'photo4' => $param['photo4'],

                ];
                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $this->_cModel->insertCases($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $this->assign(['type_name_map' => $this->_type]);

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }

    }

    /**
     * 生成缩略图
     * @param string $photo_path 原图片路径
     * @return bool
     */
    public function _getThumb($photo_path)
    {
        $image = \think\Image::open(ROOT_PATH . 'public/uploads/' . $photo_path);
        $type = $image->type();
        $card_pic_thumb = md5($photo_path) . '.' . $type;
        $image->thumb(690, 388, \think\Image::THUMB_SCALING)->save(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'thumb' . DS . $card_pic_thumb);

        return $card_pic_thumb;
    }

    //添加封面图片
    public function addPhoto()
    {
        try {
            if (!request()->file()) {
                throw new Exception('上传图片失败');
            }
            else {

                //判断上张图是否正在使用，无，则删除
                if (input('last_photo_path')) {

                    $last_photo_path = input('last_photo_path');

                    $count = $this->_cModel->getPhotoPath($last_photo_path);

                    if (!$count) {
                        if(is_file(ROOT_PATH . 'public/uploads/' . $last_photo_path)){
                            unlink(ROOT_PATH . 'public/uploads/' . $last_photo_path);
                        }
                    }
                }

                $file = request()->file('upload_file');
                $info = $file->validate(['size' => 5242880, 'ext' => 'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');

                if ($info) {
                    $photo_path = $info->getSaveName();

                    return json(['code' => 1, 'data' => $photo_path, 'msg' => '上传成功']);
                }
                else {
                    // 上传失败获取错误信息
                    $err_msg = $file->getError();

                    return json(['code' => 0, 'data' => '', 'msg' => $err_msg]);
                }

            }

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
        }
    }

    //添加详情图片
    public function addDetailPhoto()
    {
        try {
            if (!request()->file()) {
                throw new Exception('上传图片失败');
            }
            else {

                //判断上张图是否正在使用，无，则删除
                if (input('last_photo_path') && input('num')) {

                    $last_photo_path = input('last_photo_path');
                    $felid = 'photo'.input('num');
                    $where = [$felid => $last_photo_path];

                    $count = $this->_cModel->getDetailPhotoPath($where);

                    if (!$count) {
                        if(is_file(ROOT_PATH . 'public/uploads/' . $last_photo_path)){
                            unlink(ROOT_PATH . 'public/uploads/' . $last_photo_path);
                        }
                    }
                }

                $file = request()->file('upload_file');
                $info = $file->validate(['size' => 5242880, 'ext' => 'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');

                if ($info) {
                    $photo_path = $info->getSaveName();

                    return json(['code' => 1, 'data' => $photo_path, 'msg' => '上传成功']);
                }
                else {
                    // 上传失败获取错误信息
                    $err_msg = $file->getError();

                    return json(['code' => 0, 'data' => '', 'msg' => $err_msg]);
                }

            }

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
        }
    }

    /**
     * 取消新增时，判断是否上传了图片，有，则删除
     * @return string
     */
    public function cancelAdd()
    {
        try {

            $photo_path = input('photo_path');

            if ($photo_path) {
                $count = $this->_cModel->getPhotoPath($photo_path);

                if (!$count) {
                    if(is_file(ROOT_PATH . 'public/uploads/' . $photo_path)){
                        unlink(ROOT_PATH . 'public/uploads/' . $photo_path);
                    }
                }
            }


            return json(['code' => 1, 'data' => '', 'msg' => '删除成功']);

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
        }
    }

    //编辑案例
    public function casesEdit()
    {
        try {
            if (request()->isPost()) {
                $param = input();
                if ($param['is_classical'] == Sys::CASES_IS_CLASSICAL) {
                    $res = $this->_cModel->classicalCount($param['type'],$param['id']);
                    if ($res > 2) {
                        throw new Exception('前台显示该类型案例已经超过三个');
                    }
                }

                //老照片
                $oldPhotoPath = $this->_cModel->getPhotoById($param['id']);
                $oldPhotoPath = $oldPhotoPath['photo'];
                if ($param['photo'] != $oldPhotoPath && $oldPhotoPath) {
                    if(is_file(ROOT_PATH . 'public/uploads/' . $oldPhotoPath)){
                        unlink(ROOT_PATH . 'public/uploads/' . $oldPhotoPath);
                    }

                }

                $arr = ['id' => $param['id'], 'title' => $param['title'], 'customer' => $param['customer'], 'product' => $param['product'], 'platform' => $param['platform'], 'actors' => $param['actors'], 'rating' => $param['rating'], 'web_rating' => $param['web_rating'], 'prize' => $param['prize'], 'is_classical' => $param['is_classical'], 'rank' => $param['rank'], 'time' => $param['time'], 'type' => $param['type'], 'content' => $param['content'], 'photo' => $param['photo'], 'photo1' => $param['photo1'], 'photo2' => $param['photo2'], 'photo3' => $param['photo3'], 'photo4' => $param['photo4'],

                ];
                $arr['update_time'] = time();
                $flag = $this->_cModel->editCases($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $casesData = $this->_cModel->getCasesById($id);

            $this->assign(['data' => $casesData, 'type_name_map' => $this->_type]);

            return $this->fetch();

        }
        catch (Exception $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }

    }

    //删除案例
    public function casesDel()
    {
        $id = input('param.id');

        //老照片
        $oldPhotoPath = $this->_cModel->getPhotoById($id);
        $oldPhotoPath = $oldPhotoPath['photo'];
        if(is_file(ROOT_PATH . 'public/uploads/' . $oldPhotoPath)){
            unlink(ROOT_PATH . 'public/uploads/' . $oldPhotoPath);
        }


        $flag = $this->_cModel->delCases($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 修改案例首页是否显示
     * @return \think\response\Json
     */
    public function status()
    {
        try {
            $id = input('param.id');

            $aData = $this->_cModel->getCasesById($id);
            if (!$aData)
            {
                throw new Exception('案例不存在');
            }

            if ($aData['is_classical'] == Sys::CASES_IS_CLASSICAL)
            {
                $aInput = [
                    'id' => $id,
                    'is_classical' => Sys::CASES_NOT_CLASSICAL
                ];
            }
            else
            {
                $aInput = [
                    'id' => $id,
                    'is_classical' => Sys::CASES_IS_CLASSICAL
                ];
            }

            $flag = $this->_cModel->classical($aInput);

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);

        } catch (Exception $e) {
            $info = $e->getMessage();
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
        }
    }
}