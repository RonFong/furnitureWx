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
use think\Request;
use app\api\model\ArticleComment as ArticleCommentModel;
use app\api\validate\ArticleComment as ArticleCommentValidate;

/**
 * （圈子） 文章评论
 * Class ArticleComment
 * @package app\api\controller\v1
 */
class ArticleComment extends BaseController
{
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ArticleCommentModel();
        $this->currentValidate = new ArticleCommentValidate();
    }


    /**
     * @api {delete} /v1/articleComment/comment  评论文章
     * @apiGroup Article
     * @apiParam {number} article_id 被评论的文章ID
     * @apiParam {string} content 评论内容
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "article_id":1,
     *      "content":"挣了5毛钱"
     * }
     *
     * @apiSuccessExample {json} 成功时的响应：
     *  {
     *      "state": 1,
     *      "msg": "success",
     *      "data": {
     *          "user_name": "自干五",
     *          "content": "挣了5毛钱",
     *          "create_time": "07-25 18:57",
     *      }
     *  }
     */
    public function comment()
    {
        $this->currentValidate->goCheck('comment');
        try {
            $result = $this->currentModel->saveData($this->data);
            if (!$result)
                exception('评论失败');
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        $this->result['data'] = $result;
        return json($this->result, 201);
    }

    /**
     * @api {delete} /v1/articleComment/replyComment  回复评论
     * @apiGroup Article
     * @apiParam {number} parent_id 被回复的评论ID
     * @apiParam {string} content 回复内容
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "parent_id":1,
     *      "content":"挣了5美分"
     * }
     *
     * @apiSuccessExample {json} 成功时的响应：
     *  {
     *      "state": 1,
     *      "msg": "success",
     *      "data": {
     *          "user_name": "白左",
     *          "content": "挣了5美分",
     *          "create_time": "07-25 18:57",
     *          "parent_user_name": "自干五"
     *      }
     *  }
     */
    public function replyComment()
    {
        $this->currentValidate->goCheck('replyComment');
        try {
            $result = $this->currentModel->saveData($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        $this->result['data'] = $result;
        return json($this->result, 201);
    }
}