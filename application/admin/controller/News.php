<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 新闻控制类
 *
 * Class News
 * @package app\admin\controller
 */
class News extends Base
{

    public $_cModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_cModel = new \app\admin\model\News();
    }

    /**
     * 新闻列表
     * @return mixed
     */
    public function lists()
    {

        if (request()->isAjax()) {

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $sort = $param['sortName'];
            $sort_order = $param['sortOrder'];

            $sortArr = explode(',', $sort);
            $sortOrderArr = explode(',', $sort_order);

            $order = 'p_order desc, create_time desc';

            if ($sortArr)
            {
                $order = [];
                foreach ($sortArr as $k=>$v)
                {
                    $order[trim($v)] = trim($sortOrderArr[$k]);
                }
            }

            $where = [];
            if (isset($param['searchText'])) {
                if ($param['searchText']['title']) {
                    $where['title'] = ['like', '%' . $param['searchText']['title'] . '%'];
                }
                if ((int)$param['searchText']['is_send'] !== 1000) {
                    $where['is_send'] = $param['searchText']['is_send'];
                }
                if ((int)$param['searchText']['is_top'] !== 1000) {
                    $where['is_top'] = $param['searchText']['is_top'];
                }
            }


            $selectResult = $this->_cModel->getNewsByWhere($where, $offset, $limit, $order);

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['time'] = substr($selectResult[$key]['time'], 0, 10);
                $selectResult[$key]['create_time'] = date('Y-m-d H:i:s', $selectResult[$key]['create_time']);
                $selectResult[$key]['is_send'] = onButton('news/status', Sys::getIsSend($selectResult[$key]['is_send']), $selectResult[$key]['is_send'], "javascript:status('" . $vo['id'] . "' , '" . $selectResult[$key]['is_send'] . "')");
                $selectResult[$key]['is_top'] = Sys::getIsTop($selectResult[$key]['is_top']);

                // $operate = ['编辑' => url('news/newsEdit', ['id' => $vo['id']]), '删除' => "javascript:newsDel('" . $vo['id'] . "')"];
                $operate = [
                    '编辑' => [
                        'href' => url('news/newsEdit', ['id' => $vo['id']]),
                        'auth' => 'news/newsedit'
                    ],
                    '删除' => [
                        'href' => "javascript:newsDel('" . $vo['id'] . "')",
                        'auth' => 'news/newsdel'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_cModel->getAllNews($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }
        $is_send_map = Sys::getIsSend();

        $this->assign('is_send_map', $is_send_map);
        return $this->fetch();
    }

    //添加新闻
    public function newsAdd()
    {
        //
        try {
            if (request()->isPost()) {
                $param = $_POST;

                //不控制推送个数
                /*if ($param['is_send'] == Sys::NEWS_IS_SEND) {
                    $res = $this->_cModel->sendCount();
                    if ($res > 3) {
                        throw new Exception('首页推送新闻已经超过4个');
                    }

                }*/

                $param['p_order'] = 0;

                if ($param['is_top'] == Sys::NEWS_IS_SEND)
                {
                    $is_top_max = $this->_cModel->getMax('p_order');

                    $param['p_order'] = is_null($is_top_max)? 1 : $is_top_max + 1;

                }

                if (!($param['link'] || $param['content'])) {
                    throw new Exception('原文链接与内容不能同时为空');
                }

                $thumb = '';
                if ($param['photo']) {
                    $thumb = $this->_getThumb($param['photo']);
                }

                $arr = ['title' => $param['title'], 'link' => $param['link'], 'is_send' => $param['is_send'], 'is_top' => $param['is_top'], 'p_order' => $param['p_order'], 'time' => $param['time'], 'discrpte' => $param['discrpte'], 'content' => $param['content'], 'photo' => $param['photo'], 'photo_thumb' => $thumb,

                ];
                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $this->_cModel->insertNews($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            return $this->fetch();

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
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
        $image->thumb(690, 434, \think\Image::THUMB_SCALING)->save(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'thumb' . DS . $card_pic_thumb);

        return $card_pic_thumb;
    }

    //添加图片
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
                        if (is_file(ROOT_PATH . 'public/uploads/' . $last_photo_path)) {
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
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
        }
    }

    /**
     * 上传内容图
     */
    public function addEditorPhoto()
    {

        if (!request()->file()) {
            return json(['success' => false, 'file_path' => '', 'msg' => '上传图片失败']);
        }
        else {
            $file = request()->file('upload_file');
            $info = $file->validate(['size' => 5242880, 'ext' => 'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'news');
            if ($info) {
                $photo_path = $info->getSaveName();

                return json(['success' => true, 'file_path' => '../../../public/news/' . $photo_path, 'msg' => '上传成功']);
            }
            else {
                // 上传失败获取错误信息
                $err_msg = $file->getError();

                return json(['success' => false, 'file_path' => '', 'msg' => $err_msg]);
            }
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
                    if (is_file(ROOT_PATH . 'public/uploads/' . $photo_path)) {
                        unlink(ROOT_PATH . 'public/uploads/' . $photo_path);
                    }
                }
            }


            return json(['code' => 1, 'data' => '', 'msg' => '删除成功']);

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
        }
    }

    //编辑新闻
    public function newsEdit()
    {
        try {

            if (request()->isPost()) {
                $param = $_POST;
                /*if ($param['is_send'] == Sys::NEWS_IS_SEND) {
                    $res = $this->_cModel->sendCount($param['id']);
                    if ($res > 3) {
                        throw new Exception('首页推送新闻已经超过4个');
                    }
                }*/

                $param['p_order'] = 0;

                if ($param['is_top'] == Sys::NEWS_IS_SEND)
                {
                    $is_top_max = $this->_cModel->getMax('p_order');

                    $param['p_order'] = is_null($is_top_max)? 1 : $is_top_max + 1;

                }

                if (!($param['link'] || $param['content'])) {
                    throw new Exception('原文链接与内容不能同时为空');
                }

                //老照片
                $oldPhotoPath = $this->_cModel->getPhotoById($param['id']);
                $oldPhotoPath = $oldPhotoPath['photo'];
                if ($param['photo'] != $oldPhotoPath && $oldPhotoPath) {
                    if (is_file(ROOT_PATH . 'public/uploads/' . $oldPhotoPath)) {
                        unlink(ROOT_PATH . 'public/uploads/' . $oldPhotoPath);
                    }
                }

                $arr = ['id' => $param['id'], 'title' => $param['title'], 'link' => $param['link'], 'is_send' => $param['is_send'], 'is_top' => $param['is_top'], 'p_order' => $param['p_order'], 'time' => $param['time'], 'discrpte' => $param['discrpte'], 'content' => $param['content'], 'photo' => $param['photo'],

                ];
                $arr['update_time'] = time();
                $flag = $this->_cModel->editNews($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $newsData = $this->_cModel->getNewsById($id);

            $this->assign(['data' => $newsData]);

            return $this->fetch();

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
        }

    }

    /**
     * 修改新闻首页是否显示
     * @return \think\response\Json
     */
    public function status()
    {
        try {
            $id = input('param.id');

            $aData = $this->_cModel->getNewsById($id);
            if (!$aData)
            {
                throw new Exception('新闻不存在');
            }

            if ($aData['is_send'] == Sys::NEWS_IS_SEND)
            {
                $aInput = [
                    'id' => $id,
                    'is_send' => Sys::NEWS_NOT_SEND
                ];
            }
            else
            {
                $aInput = [
                    'id' => $id,
                    'is_send' => Sys::NEWS_IS_SEND
                ];
            }

            $flag = $this->_cModel->send($aInput);

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);

        } catch (Exception $e) {
            $info = $e->getMessage();
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
        }
    }

    //删除新闻
    public function newsDel()
    {
        $id = input('param.id');
        //老照片
        $oldPhotoPath = $this->_cModel->getPhotoById($id);
        $oldPhotoPath = $oldPhotoPath['photo'];
        if (is_file(ROOT_PATH . 'public/uploads/' . $oldPhotoPath)) {
            unlink(ROOT_PATH . 'public/uploads/' . $oldPhotoPath);
        }


        $flag = $this->_cModel->delNews($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 首页图片
     */
    public function newsPhoto()
    {
        $imgModel = new \app\admin\model\Img();
        if (request()->isAjax()) {

            $param = input('param.');
            $arr = [
                'id' => $param['id'],
                'type' => $param['type']?:0,
                'mobile' => $param['mobile']?:0,
                'photo' => $param['photo'],
                'update_time' => time()
            ];
            $flag = $imgModel->updateImg($arr);

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        // 新闻图
        $newsPhoto = $imgModel->getPhoto(['type' => Sys::IMG_IS_NEWS]);
        $this->assign('newsPhoto', $newsPhoto);

        // 合作伙伴图
        $partnerPCPhoto = $imgModel->getPhoto(['type' => Sys::IMG_IS_PARTNER, 'mobile' => Sys::IS_PC]);
        $partnerMBPhoto = $imgModel->getPhoto(['type' => Sys::IMG_IS_PARTNER, 'mobile' => Sys::IS_MOBILE]);
        $this->assign('partPCPhoto', $partnerPCPhoto);
        $this->assign('partMBPhoto', $partnerMBPhoto);

        // 公众号二维码
        $pubPhoto = $imgModel->getPhoto(['type' => Sys::IMG_IS_PUBLIC]);
        $this->assign('pubPhoto', $pubPhoto);

        return $this->fetch();
    }
}