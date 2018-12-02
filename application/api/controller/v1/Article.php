<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Article as ArticleModel;
use app\api\validate\Article as ArticleValidate;
use app\common\model\ArticleClassify;
use app\lib\enum\Response;
use think\Request;

/**
 * 圈子 - 文章
 * Class Article
 * @package app\api\controller\v1
 */
class Article extends BaseController
{

    /**
     * 图片所在文件夹 （static/img 下）
     * @var string
     */
    private $folder;

    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel    = new ArticleModel();
        $this->currentValidate = new ArticleValidate();
        $this->folder = "article";
    }

    /**
     * @api      {get} /v1/article/classify  获取文章分类
     * @apiGroup Article
     * @apiParam {number} [parent_id] 分类的父ID (当前不传或只传0)
     *
     * @apiParamExample  {string} 请求参数格式：
     * 略
     *
     * @apiSuccessExample {json} 成功时的数据：
     * {
     *      "data": [
     *          {
     *              "id": 1,
     *              "classify_name": "秀家",
     *              "classify_img": "/home.png"
     *          },
     *          {
     *              "id": 2,
     *              "classify_name": "招聘",
     *              "classify_img": "/home.png"
     *          },
     *          {
     *              "id": 3,
     *              "classify_name": "其他",
     *              "classify_img": "/home.png"
     *          }
     *      ]
     */
    public function getClassify()
    {

        $parentId = array_key_exists('parent_id', $this->data) ? (int)$this->data['parent_id'] : 0;
        $this->result['data'] = ArticleClassify::getClassify($parentId);

        return json($this->result, 200);
    }


    /**
     * @api      {post} /v1/article/create  创建文章
     * @apiGroup Article
     * @apiParam {string} [music] 背景音乐
     * @apiParam {string} classify_id 分类id
     * @apiParam {array} content 内容集
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     * "classify_id":1,
     * "music:"url****",    //通过音乐接口获得
     * "content":[
     *      {
     *          "sort":1,
     *          "img":"/static/img/tmp/90817f4f9e8fc6aa3ab09aa15e408b45.jpeg",
     *          "text":"队长，别点火！我甩不开"
     *      },
     *      {
     *          "sort":2,
     *          "img":"/static/img/tmp/90817f4f9e8fc6aa3ab09aa15e408b45.jpeg",
     *          "text":"队长，别点火！我甩不开2"
     *      }
     * }
     * @apiSuccessExample {json} 成功时的数据：
     * {
     *      "state":1,
     *      "msg":"success",
     *      "data":"12"     //所写入数据的id
     * }
     */
    /**
     * @return mixed
     * @throws \app\lib\exception\BaseException
     */
    public function create()
    {
        $this->currentValidate->goCheck('create');
        $this->result['data'] = $this->currentModel->createData($this->data);
        if (!$this->result['data']) {
            $this->response->error(Response::UNKNOWN_ERROR);
        }
        return json($this->result, 201);
    }


    /**
     * 修改圈子文章
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function update()
    {
        $this->currentValidate->goCheck('update');
        $result = $this->currentModel->updateData($this->data);
        if (!$result) {
            $this->response->error(Response::UNKNOWN_ERROR);
        }
        return json($this->result, 201);
    }


    /**
     * @api      {delete} /v1/article/delete  删除文章
     * @apiGroup Article
     * @apiParam {number} id 文章id
     *
     * @apiParamExample  {string} 请求参数格式：
     * {"id":1}
     *
     * @apiSuccessExample {json} 成功时的数据：
     * {
     *      "state":1,
     *      "msg":"success",
     *      "data":""
     * }
     */
    public function delete()
    {
        $this->currentValidate->goCheck('delete');
        $result = $this->currentModel->deleteData($this->data['id']);
        if ($result !== true) {
            $this->result['state'] = 0;
            $this->result['msg']   = $result;
            $code                  = 400;
        }

        return json($this->result, $code ?? 200);
    }

    /**
     * 获取文章列表
     * @param order_by string  read_num,create_time,distance     排序： 人气、时间、距离
     * @param classify_id number    分类id
     * @param keyword string    title模糊查询关键字
     * @param page number    页码
     * @param row number    每页显示数据条数
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function list()
    {
        $this->currentValidate->goCheck('list');
        try {
            $this->result['data'] = $this->currentModel->list($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }


    /**
     * 获取指定用户的文章列表
     * @param user_id number 有值为获取该用户， 空为获取自己的列表
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function listGroupByUser()
    {
        try {
            if (empty($this->data['user_id'])) {
                $this->data['user_id'] = user_info('id');
            }
            $this->result['data'] = $this->currentModel->list($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * 获取文章详情
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function details()
    {
        $this->currentValidate->goCheck('details');
        try {
            $this->result['data'] = $this->currentModel->details($this->data['id']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * @api      {put} /v1/article/share  文章分享数 + 1
     * @apiGroup Article
     * @apiParam {number} id 文章id
     *
     * @apiParamExample  {string} 请求参数格式：
     * {"id":1}
     *
     * @apiSuccessExample {json} 成功时的数据：
     *      "state":1,
     *      "msg":"success",
     *      "data":""
     * }
     */
    public function share()
    {

        $this->currentValidate->goCheck('share');
        try {
            $this->currentModel->share($this->data['id']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

    /**
     * @api      {get} /v1/article/myCollect  我关注的用户
     * @apiGroup Article
     *
     * @apiParam {number} page 页码
     * @apiParam {number} row 条目数
     *
     * @apiParamExample  {string} 请求参数格式：
     * 略
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *   "state": 1,
     *   "msg": "success",
     *   "data": {
     *   "total": "2",                   //总关注数
     *   "list":
     *       [
     *           {
     *               "id": 1,
     *               "user_name": "MT",
     *               "avatar": "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png",
     *               "is_together": true    //是否互相关注
     *           },
     *           {
     *               "id": 17,
     *               "user_name": "jinkela",
     *               "avatar":
     *               "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132",
     *              "is_together": false
     *           }
     *       ]
     *   }
     *}
     */
    public function myCollect()
    {
        try {
            $this->result['data'] = $this->currentModel->myCollect($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }


    /**
     * 文章收藏列表
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function articleCollectList()
    {
        try {
            $this->result['data'] = $this->currentModel->articleCollectList($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

    /**
     * @api      {get} /v1/article/collectMe  我的粉丝
     * @apiGroup Article
     *
     * @apiParam {number} page 页码
     * @apiParam {number} row 条目数
     *
     * @apiParamExample  {string} 请求参数格式：
     * 略
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *   "state": 1,
     *   "msg": "success",
     *   "data": {
     *   "total": "2",                   //总粉丝数
     *   "list":
     *       [
     *           {
     *               "id": 1,
     *               "user_name": "MT",
     *               "avatar": "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png"
     *           },
     *           {
     *               "id": 17,
     *               "user_name": "jinkela",
     *               "avatar":
     *               "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132",
     *           }
     *       ]
     *   }
     *}
     */
    public function collectMe()
    {
        try {
            $this->result['data'] = $this->currentModel->collectMe($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }


}