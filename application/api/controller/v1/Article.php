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
use app\api\service\ContentCensor;
use think\Request;
use app\api\model\Article as ArticleModel;

class Article extends BaseController
{
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ArticleModel();
        $this->currentValidate = validate('Article');
    }

    public function create(ContentCensor $contentCensor)
    {
         dump($contentCensor->text($this->data['content']));
    }
}