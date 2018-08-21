<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/21 
// +----------------------------------------------------------------------


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\common\validate\UserProposed as UserProposedValidate;
use app\api\model\UserProposed as UserProposedModel;
use think\Request;

/**
 * 用户推荐
 * Class UserProposed
 * @package app\api\controller\v1
 */
class UserProposed extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new UserProposedModel();
        $this->currentValidate = new UserProposedValidate();
    }


    /**
     * @api {post} /v1/userProposed/proposed  保存推荐关系 (厂/商家用户在 某用户的推荐地址上申请注册)
     * @apiGroup UserProposed
     * @apiParam {number} [user_id] 推荐人ID
     *
     * @apiParamExample  {string} 请求参数格式：
     * 略
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function proposed()
    {
        try {
            $this->currentValidate->goCheck('proposed');

            $this->data['proposed_id'] = user_info('id');
            $this->data['create_time'] = time();

            $this->currentModel->save($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 201);
    }



        /**
         * @api {get} /v1/userProposed/proposedList  用户的推荐列表
         * @apiGroup UserProposed
         * @apiParam {number}  page
         * @apiParam {number}  row
         *
         * @apiParamExample  {string} 请求参数格式：
         * 略
         *
         * @apiSuccessExample {json} 成功时的数据：
         *{
         *    "state": 1,
         *    "msg": "success",
         *    "data": [
         *        {
         *            "type": 1,                    //类型    1 厂家   2 经销商
         *            "group_id": 9,                    // id
         *            "create_time": "2018-08-22",    //推荐注册时间
         *            "group_name": "双虎家居",
         *            "proposed_money": 300           //提成
         *        },
         *        {
         *            "type": 2,
         *            "group_id": 14,
         *            "create_time": "2018-08-21",
         *            "group_name": "潘峰家具城",
         *            "proposed_money": 500
         *        }
         *    ]
         *}
         */
        public function proposedList()
        {
            try {
                $this->result['data'] = $this->currentModel->getProposedList($this->page, $this->row);
            } catch (\Exception $e) {
                $this->response->error($e);
            }
            return json($this->result, 200);
        }


}