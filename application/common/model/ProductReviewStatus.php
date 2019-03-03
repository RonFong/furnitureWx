<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/3/3 
// +----------------------------------------------------------------------


namespace app\common\model;


class ProductReviewStatus extends Model
{

    protected $remark = [
        0   => '99家服务人员将尽快处理，请稍等~',
        1   => '已同步到商城，输入编号，就可找到这款产品哦~',
    ];

    /**
     * 写入审核进度
     * @param $productId
     * @param $status
     * @param string $remark
     * @return bool
     * @throws \think\exception\DbException
     */
    public function write($productId, $status, $remark = '')
    {
        self::save([
            'product_id'    => $productId,
            'status'        => $status,
            'remark'        => $remark ?? $this->remark[$status]
        ]);
        return true;
    }

}