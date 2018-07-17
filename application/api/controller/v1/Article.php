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
use app\api\service\ImageText;
use app\common\model\ArticleClassify;
use app\common\model\ArticleContent;
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
        $this->currentModel = new ArticleModel();
        $this->currentValidate = new ArticleValidate();
        $this->folder = "article";
    }

    /**
     * @api {get} /v1/article/classify  获取文章分类
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
     *              "classify_name": "秀家"
     *          },
     *          {
     *              "id": 2,
     *              "classify_name": "招聘"
     *          },
     *          {
     *              "id": 3,
     *              "classify_name": "其他"
     *          }
     *      ]
     */
    public function getClassify()
    {
        $parentId = array_key_exists('parent_id', $this->data) ? (int) $this->data['parent_id'] : 0;
        $this->result['data'] = ArticleClassify::getClassify($parentId);
        return json($this->result, 200);
    }


    /**
     * @api {post} /v1/article/create  创建文章
     * @apiGroup Article
     * @apiParam {string} title 标题
     * @apiParam {string} [music] 背景音乐
     * @apiParam {string} classify_id 分类id
     * @apiParam {array} content 内容集
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     * "title":"如何在炸药包上贴双面胶",
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
    public function create()
    {
        $this->currentValidate->goCheck('create');
        $result = (new ImageText($this->currentModel, new ArticleContent(), $this->folder))::write($this->data);
        if (!$result['state']) {
            $this->result['state'] = 0;
            $this->result['msg'] = $result['msg'];
            $code = 400;
        } else {
            $this->result['data'] = $result['id'];
        }
        return json($this->result, $code ?? 200);
    }


    /**
     * @api {put} /v1/article/update  更新文章
     * @apiGroup Article
     * @apiParam {number} id 文章id
     * @apiParam {string} title 标题
     * @apiParam {string} [music] 背景音乐
     * @apiParam {string} classify_id 分类id
     * @apiParam {array} content 内容集
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     * "id":1,
     * "title":"如何在炸药包上贴双面胶",
     * "classify_id":1,
     * "music":"url****",    //通过音乐接口获得
     * "content":[
     *      {
     *          "id":2,         //有id，为更新
     *          "sort":1,
     *          "img":"/static/img/tmp/90817f4f9e8fc6aa3ab09aa15e408b45.jpeg",
     *          "text":"队长，别点火！我甩不开"
     *      },
     *      {
     *          //无id,为新增
     *          "sort":2,
     *          "img":"/static/img/tmp/90817f4f9e8fc6aa3ab09aa15e408b45.jpeg",
     *          "text":"队长，别点火！我甩不开2"
     *      }
     *      //存在数据库，但id不在提交的数据中的，为删除
     * }
     * @apiSuccessExample {json} 成功时的数据：
     * {
     *      "state":1,
     *      "msg":"success",
     *      "data":"12"     //所写入数据的id
     * }
     */
    public function update()
    {
        $this->currentValidate->goCheck('update');
        $result = (new ImageText($this->currentModel, new ArticleContent(), $this->folder))::write($this->data);
        if (!$result['state']) {
            $this->result['state'] = 0;
            $this->result['msg'] = $result['msg'];
            $code = 500;
        } else {
            $this->result['data'] = $result['id'];
        }
        return json($this->result, $code ?? 200);
    }

    /**
     * @api {delete} /v1/article/delete  删除文章
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
            $this->result['msg'] = $result;
            $code = 400;
        }
        return json($this->result, $code ?? 200);
    }

}