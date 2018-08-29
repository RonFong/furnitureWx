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
use app\api\model\ArticleComment;
use app\lib\enum\Response;
use Carbon\Carbon;
use think\Cache;
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
        $this->currentModel->setPage($this->page, $this->row);
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

        $parentId             = array_key_exists('parent_id', $this->data) ? (int)$this->data['parent_id'] : 0;
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
        $result = (new ImageText())
            ->setMainModel($this->currentModel)//设置图文主模型
            ->setContentModel(new ArticleContent())//设置图文块模型
            ->setImgFolder($this->folder)//设置图片保存地址
            ->write($this->data);                       //写入数据
        if (!$result['state']) {
            $this->result['state'] = 0;
            $this->result['msg']   = $result['msg'];
            $code                  = 400;
        } else {
            $this->result['data'] = $result['id'];
        }

        return json($this->result, $code ?? 200);
    }


    public function queryArticleList($query)
    {
        if (!array_key_exists('type', $this->data)) {
            $this->response->error(Response::RELATE_ERROR);
        }

        //获取首页列表
        if ($this->data['type'] == 'homePage') {
            return $this->localArticleList();
        }

        //获取本人文章列表
        if ($this->data['type'] == 'self') {
            return $this->getOwnArticleList();
        }

        //根据用户ID获取文章列表
        if ($this->data['type'] == 'byUid') {
            return $this->getOwnArticleList();
        }

        //根据用户ID获取文章列表
        if ($this->data['type'] == 'classify') {
            return $this->getListByClassify();
        }
    }

    /**
     * @api      {get} /v1/article/localList  附近和已关注用户的动态
     * @apiGroup Article
     *
     * @apiParam {number} [page] 页码
     * @apiParam {number} [row] 每页条目数
     * @apiParam {number} [order] 排序 默认0 ; 0 最新， 1 人气， 2 最近， 3 回复
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "page":1,
     *      "row":10
     * }
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": [
     *  {
     *      "id": 2,
     *      "user_name": "test2",
     *      "avatar":
     *      "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132",
     *      "create_time": 1531596561,
     *      "classify_name": "秀家",
     *      "pageview": 6,          //查看数
     *      "great_total": 0,       //点赞数
     *      "comment_total": 0,     //评论数
     *      "content": {
     *          "text": "队长，别点火！我甩不脱！ssdsddd",      //第一个文字内容块中的文字
     *          "img": [           //最多显示三张，没有则为空
     *                  "/static/img/article/cb2c82738fbe9165e94cadc6aada77ae.jpeg",
     *                  "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png",
     *                  "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png"
     *              ]
     *          }
     *      }
     * }
     *}
     */
    public function localArticleList()
    {

        $this->currentValidate->goCheck('localArticleList');
        try {
            $this->result['data'] = $this->currentModel->localArticleList('', $this->data['order']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

    /**
     * @api      {get} /v1/article/details  根据id 获取文章详情
     * @apiGroup Article
     *
     * @apiParam {number} id 文章id
     *
     * @apiParamExample  {string} 请求参数格式：
     * 略
     *
     * @apiSuccessExample {json} 成功时的响应：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": {
     *  "id": 1,                                //文章id
     *  "user_id": 1,                           //作者id
     *  "user_name": "Trump",                   //作者昵称
     *  "avatar": "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png",   //头像
     *  "create_time": "2018-07-15 03:29:15",    //发布时间
     *  "music":
     *  "http://zhangmenshiting.qianqian.com/data2/music/dcd350d9c095d40d276914eece786513/594668014/594668014.mp3?xcode=40e5a4864e417ada180b9e6dd2675aac",
     *  "classify_name": "秀家",                 //分类名
     *  "pageview": 5,                          //阅读数
     *  "great_total": 5,                       //点赞数
     *  "comment_total": 5,                     //评论数
     *  "is_self": false,                       //是否为当前用户自己发布的文章！
     *  "content":                              //文章图文内容
     *      [
     *          {
     *              "img": "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png",
     *              "text": "我就是我",
     *              "sort": 1
     *          },
     *          {
     *              "img": "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png",
     *              "text": "wewewewe",
     *              "sort": 1
     *          }
     *      ],
     *  "comments": [                           //文章的评论
     *          {
     *              "id": 22,                   //评论id
     *              "user_id": 16,              //评论人id
     *              "user_name": "test2",       //评论人昵称
     *              "avatar":
     *              "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132",
     *              "content": "挣了5毛钱22",
     *              "great_total": 1,
     *              "create_time": 1532515666,
     *          "child": [                      //评论的回复
     *              {
     *                  "id": 23,
     *                  "user_id": 17,
     *                  "user_name": "user2",
     *                  "respondent_user_name": "",     //所回复的评论的发布人昵称   （首条回复，值为空）
     *                  "reply_content": "评论的回复"    //回复内容
     *              },
     *              {
     *                  "id": 24,
     *                  "user_id": 16,
     *                  "user_name": "test2",
     *                  "respondent_user_name": "Jack",   //所回复的评论的发布人昵称
     *                  "reply_content": "回复的回复"      //回复内容
     *              }
     *          ]
     *      },
     *      {
     *          "user_id": 16,
     *          "user_name": "test2",
     *          "avatar":
     *          "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132",
     *          "content": "挣了5毛钱",
     *          "great_total": 0,
     *          "create_time": 1532515780,
     *          "id": 25,
     *          "child": []                               //此评论无回复
     *          }
     *      ]
     *  }
     *}
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
     * @api      {get} /v1/article/moreComment  获取文章更多评论
     * @apiGroup Article
     *
     * @apiParam {number} article_id 文章id
     * @apiParam {number} page 页码
     * @apiParam {number} row 每页条目数
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "article_id":1
     *      "page":2,      //文章详情已输出10条评论， page 参数值可从 2 开始
     *      "row":10
     * }
     *
     * @apiSuccessExample {json} 成功时的响应：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": [                           //文章的评论
     *          {
     *              "id": 22,                   //评论id
     *              "user_id": 16,              //评论人id
     *              "user_name": "test2",       //评论人昵称
     *              "avatar":
     *              "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132",
     *              "content": "挣了5毛钱22",
     *              "great_total": 1,
     *              "create_time": 1532515666,
     *          "child": [                      //评论的回复
     *              {
     *                  "id": 23,
     *                  "user_id": 17,
     *                  "user_name": "user2",
     *                  "respondent_user_name": "",     //所回复的评论的发布人昵称   （首条回复，值为空）
     *                  "reply_content": "评论的回复"    //回复内容
     *              },
     *              {
     *                  "id": 24,
     *                  "user_id": 16,
     *                  "user_name": "test2",
     *                  "respondent_user_name": "Jack",   //所回复的评论的发布人昵称
     *                  "reply_content": "回复的回复"      //回复内容
     *              }
     *          ]
     *      },
     *      {
     *          "user_id": 16,
     *          "user_name": "test2",
     *          "avatar":
     *          "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132",
     *          "content": "挣了5毛钱",
     *          "great_total": 0,
     *          "create_time": 1532515780,
     *          "id": 25,
     *          "child": []                               //此评论无回复
     *          }
     *      ]
     *  }
     *}
     */
    public function getMoreComment()
    {

        $this->currentValidate->goCheck('moreComment');
        try {
            $this->result['data'] = (new ArticleComment())->getComments($this->data['article_id'], $this->page, $this->row);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

    /**
     * @api      {get} /v1/article/ownList  获取自己的文章列表
     * @apiGroup Article
     *
     * @apiParam {number} page 页码
     * @apiParam {number} row 每页条目数
     *
     * @apiParamExample  {string} 请求参数格式：
     * 无
     *
     * @apiSuccessExample {json} 成功时的响应：
     *  {
     *      "state": 1,
     *      "msg": "success",
     *      "data": [
     *          {
     *              "id": 1,
     *              "user_name": "test2",
     *              "avatar":
     *              "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132",
     *              "create_time": "2018-07-15 03:29:15",
     *              "classify_name": "秀家",
     *              "pageview": 5,
     *              "great_total": 5,
     *              "comment_total": 5,
     *              "content": {
     *              "text": "我就是我",
     *              "img": [
     *                  "fc38c299804217dfaf0ab4d04fbf0093.gif",
     *                  "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png",
     *                  "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png"
     *              ]
     *          }
     *      ]
     *   }
     */
    public function getOwnArticleList()
    {

        try {
            $this->result['data'] = $this->currentModel->getListByUserId(user_info('id'));
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

    /**
     * @api      {get} /v1/article/listByClassify  根据分类获取文章
     * @apiGroup Article
     *
     * @apiParam {number} classify_id 分类id
     * @apiParam {number} [order] 排序 默认0 ; 0 最新， 1 人气， 2 最近， 3 回复
     * @apiParam {number} page 页码
     * @apiParam {number} row 每页条目数
     *
     * @apiParamExample  {string} 请求参数格式：
     * {"classify_id":1,"page":1,"row":10}
     *
     * @apiSuccessExample {json} 成功时的响应：
     *  {
     *      // 同  ownList 接口
     *  }
     */
    public function getListByClassify()
    {

        $this->currentValidate->goCheck('listByClassify');
        try {
            $this->result['data'] = $this->currentModel->getListByClassify($this->data['classify_id'], $this->data['order']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

    /**
     * @api      {get} /v1/article/myCollect  我收藏的文章
     * @apiGroup Article
     *
     * @apiParam {number} page 页码
     * @apiParam {number} row 每页条目数
     *
     * @apiParamExample  {string} 请求参数格式：
     * {"page":1,"row":10}
     *
     * @apiSuccessExample {json} 成功时的响应：
     *  {
     *      // 同  ownList 接口
     *  }
     */
    public function myCollectArticle()
    {

        try {
            $this->result['data'] = $this->currentModel->myCollectArticle();
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

    /**
     * @api      {get} /v1/article/ownList  根据用户id获取文章列表
     * @apiGroup Article
     *
     * @apiParam {number}  user_id   用户id
     *
     * @apiParamExample  {string} 请求参数格式：
     *  {"user_id":16}
     *
     * @apiSuccessExample {json} 成功时的响应：
     *  {
     *      "state": 1,
     *      "msg": "success",
     *      "data": [
     *          {
     *              "id": 1,
     *              "user_name": "test2",
     *              "avatar":
     *              "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132",
     *              "create_time": "2018-07-15 03:29:15",
     *              "classify_name": "秀家",
     *              "pageview": 5,
     *              "great_total": 5,
     *              "comment_total": 5,
     *              "content": {
     *              "text": "我就是我",
     *              "img": [
     *                  "fc38c299804217dfaf0ab4d04fbf0093.gif",
     *                  "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png",
     *                  "/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png"
     *              ]
     *          }
     *      ]
     *   }
     */
    public function getArticleListByUserId()
    {

        $this->currentValidate->goCheck('getByUserId');
        try {
            $this->result['data'] = $this->currentModel->getListByUserId($this->data['user_id']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

    /**
     * 根据用户id获取文章
     * @param $userId
     * @return array
     * @throws \think\exception\DbException
     */
    protected function getListByUserId($userId)
    {

        return $this->currentModel->getListByUserId($userId);
    }

    /**
     * @api      {put} /v1/article/update  更新文章
     * @apiGroup Article
     * @apiParam {number} id 文章id
     * @apiParam {string} [music] 背景音乐
     * @apiParam {string} classify_id 分类id
     * @apiParam {array} content 内容集
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     * "id":1,
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
        $result = (new ImageText())
            ->setMainModel($this->currentModel)//设置图文主模型
            ->setContentModel(new ArticleContent())//设置图文块模型
            ->setImgFolder($this->folder)//设置图片保存地址
            ->write($this->data);                       //传入数据
        if (!$result['state']) {
            $this->result['state'] = 0;
            $this->result['msg']   = $result['msg'];
            $code                  = 500;
        } else {
            $this->result['data'] = $result['id'];
        }

        return json($this->result, $code ?? 200);
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
            $this->result['data'] = $this->currentModel->myCollect();
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
            $this->result['data'] = $this->currentModel->collectMe();
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

    public function setCache()
    {

        $setCacheData = [
            'itemKey'    => $this->request->param('itemKey', ''),
            'text'       => $this->request->param('text', false),
            'img'        => $this->request->param('img', false),
            'articleId'  => $this->request->param('articleId', ''),
            'classifyId' => $this->request->param('classifyId', ''),
            'music'      => $this->request->param('music', false),
            'musicName'  => $this->request->param('musicName', false),
            'userId'     => user_info('id'),
            'type'       => $this->request->param('type', 1),
        ];
        \app\api\model\ArticleContent::setCache($setCacheData);

        return json($this->result);
    }

    public function getCache()
    {

        $setCacheData         = [
            'itemKey'   => $this->request->param('itemKey', ''),
            'articleId' => $this->request->param('articleId'),
            'userId'    => user_info('id'),
            'type'      => $this->request->param('type', 1),
        ];
        $data                 = \app\api\model\ArticleContent::getCache($setCacheData);
        $this->result['data'] = $data;

        return json($this->result);
    }

    /**
     * 获取文章部分详情（文字编辑）
     * @return mixed
     * @throws \app\lib\exception\BaseException
     */
    public function getArticleContent()
    {

        $this->currentValidate->goCheck('details');
        try {
            $this->result['data'] = $this->currentModel->getArticleContent(['articleId' => $this->data['id']]);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }

}