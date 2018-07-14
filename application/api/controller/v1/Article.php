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
use app\api\service\ImageText;
use app\api\validate\Article as ArticleValidate;
use app\common\model\ArticleClassify;
use think\Request;

/**
 * 圈子 - 文章
 * Class Article
 * @package app\api\controller\v1
 */
class Article extends BaseController
{
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ArticleModel();
        $this->currentValidate = new ArticleValidate();
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

    public function create()
    {
        $this->currentValidate->goCheck('create');

        $result = ImageText::create($this->data, $this->files);
        die;
    }

}