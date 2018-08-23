<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\HomeContent as homeContentModel;
use app\api\model\HomeContentItem;
use app\lib\enum\Response;
use think\Cache;
use think\Request;

class HomeContent extends BaseController
{

    /**
     * 参数校验统一入口方法
     * HomeContent constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {

        parent::__construct($request);
        $this->currentModel    = new homeContentModel();
        $this->currentValidate = validate('homeContent');
    }

    public function addHomeContent()
    {

        $homeContentModel             = new homeContentModel();
        $homeContentModel->group_id   = 2;
        $homeContentModel->group_type = 1;
        $homeContentModel->music      = 'http://www.7qiaoban.cn/test_music.mp3';
        $homeContentModel->record     = 'http://www.7qiaoban.cn/test_music.mp3';
        $homeContentModel->save();
        $homeContentItemModel             = new HomeContentItem();
        $homeContentItemModel->content_id = $homeContentModel->id;
        $homeContentItemModel->text       = $this->request->post('text');
        $homeContentItemModel->save();

        return json_encode(['code' => 1, 'msg' => '添加成功']);
    }

    public function saveHomeContent()
    {

        $saveContentData = [
            'groupId'   => user_info('group_id'),
            'groupType' => user_info('type'),
            'music'     => $this->request->param('music'),
            'record'    => $this->request->param('record'),
            'musicName' => $this->request->param('music_name'),
            'items'     => $this->request->param('items'),
        ];
        HomeContentItem::saveContent($saveContentData);

        return json($this->result);
    }

    public function getHomeContent()
    {

        $getContentData       = [
            'groupId'   => user_info('group_id'),
            'groupType' => user_info('type'),
        ];
        $data                 = HomeContentItem::getContent($getContentData);
        $this->result['data'] = $data;

        return json($this->result);
    }

    public function getHomeContentItem()
    {

        $getContentItemData   = [
            'itemId' => $this->request->param('itemId'),
        ];
        $data                 = HomeContentItem::getContentItem($getContentItemData);
        $this->result['data'] = $data;

        return json($this->result);
    }

    public function setCache()
    {

        $setCacheData = [
            'itemKey'   => $this->request->param('itemKey', ''),
            'text'      => $this->request->param('text', false),
            'img'       => $this->request->param('img', false),
            'music'     => $this->request->param('music', false),
            'musicName' => $this->request->param('music_name', false),
            'groupId'   => user_info('group_id'),
            'groupType' => user_info('type'),
            'type'      => $this->request->param('type', 1),
        ];
        HomeContentItem::setCache($setCacheData);
//        $this->result['data'] = [
//            'groupType' => $setCacheData['groupType']
//        ];
        return json($this->result);
    }

    public function getCache()
    {

        $setCacheData         = [
            'itemKey'   => $this->request->param('itemKey', ''),
            'groupId'   => user_info('group_id'),
            'groupType' => user_info('type'),
            'type'      => $this->request->param('type', 1),
        ];
        $data                 = HomeContentItem::getCache($setCacheData);
        $this->result['data'] = $data;

        return json($this->result);
    }

}