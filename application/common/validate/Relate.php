<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/7/16 
// +----------------------------------------------------------------------


namespace app\common\validate;


use app\common\model\RelationArticleCollect;
use app\common\model\RelationArticleGreat;
use app\common\model\RelationCommentGreat;
use app\common\model\RelationFactoryCollect;
use app\common\model\RelationGoodsCollect;
use app\common\model\RelationShopCollect;
use app\common\model\RelationUserCollect;

class Relate extends BaseValidate
{
    protected $rule = [
        'article_id'    => 'require|number',
        'comment_id'    => 'require|number',
        'factory_id'    => 'require|number',
        'shop_id'       => 'require|number',
        'goods_id'      => 'require|number',
        'other_user_id' => 'require|number',
        'type'          => 'require|in:inc,dec',
    ];

    protected $scene = [
        'articleCollect' => [
            'article_id',
            'type'       => 'require|in:inc,dec|articleCollect',
        ],
        'articleGreat' => [
            'article_id',
            'type'       => 'require|in:inc,dec|articleGreat',
        ],
        'commentGreat' => [
            'comment_id',
            'type'       => 'require|in:inc,dec|commentGreat',
        ],
        'userCollect' => [
            'other_user_id',
            'type'       => 'require|in:inc,dec|userCollect',
        ],
        'goodsCollect' => [
            'goods_id',
            'type'       => 'require|in:inc,dec|goodsCollect',

        ],
        'factoryBlacklist' => [
            'shop_id',
            'type'       => 'require|in:inc,dec|goodsCollect',

        ],
        'goodsBlacklist' => [
            'goods_id',
            'type'       => 'require|in:inc,dec|goodsCollect',

        ],
        'shopBlacklist' => [
            'factory_id',
            'type'       => 'require|in:inc,dec|goodsCollect',
        ],
        'shopCollect'   => [
            'shop_id',
            'type'      => 'require|in:inc,dec|shopCollect',
        ],
        'factoryCollect'   => [
            'factory_id',
            'type'      => 'require|in:inc,dec|factoryCollect',
        ],
    ];

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\exception\DbException
     */
    protected function shopCollect($value, $rule, $data)
    {
        $isExist =  RelationShopCollect::get([
            'user_id' => user_info('id'),
            'shop_id' => $data['shop_id']
        ]);
        if ($data['type'] == 'inc' && $isExist) {
            return '请勿重复收藏';
        } elseif ($data['type'] == 'dec' && !$isExist) {
            return '此商家未被收藏';
        }
        return true;
    }

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\exception\DbException
     */
    protected function factoryCollect($value, $rule, $data)
    {
        $isExist =  RelationFactoryCollect::get([
            'user_id' => user_info('id'),
            'factory_id' => $data['factory_id']
        ]);
        if ($data['type'] == 'inc' && $isExist) {
            return '请勿重复收藏';
        } elseif ($data['type'] == 'dec' && !$isExist) {
            return '此厂家未被收藏';
        }
        return true;
    }

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function articleCollect($value, $rule, $data)
    {
        try {
            $isExist =  RelationArticleCollect::get([
                'user_id' => user_info('id'),
                'article_id' => $data['article_id']
            ]);
            if ($data['type'] == 'inc' && $isExist) {
                return '请勿重复收藏';
            } elseif ($data['type'] == 'dec' && !$isExist) {
                return '此文章未被收藏';
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function articleGreat($value, $rule, $data)
    {
        try {
            $isExist =  RelationArticleGreat::get([
                'user_id' => user_info('id'),
                'article_id' => $data['article_id']
            ]);
            if ($data['type'] == 'inc' && $isExist) {
//                return '请勿重复点赞';
                return config('system.msg_for_frequently');
            } elseif ($data['type'] == 'dec' && !$isExist) {
//                return '此文章未被点赞';
                return config('system.msg_for_frequently');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function commentGreat($value, $rule, $data)
    {
        try {
            $isExist =  RelationCommentGreat::get([
                'user_id' => user_info('id'),
                'comment_id' => $data['comment_id']
            ]);
            if ($data['type'] == 'inc' && $isExist) {
                return '请勿重复点赞';
            } elseif ($data['type'] == 'dec' && !$isExist) {
                return '此文评论未被点赞';
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }


    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function goodsCollect($value, $rule, $data)
    {
        try {
            $isExist =  RelationGoodsCollect::get([
                'user_id' => user_info('id'),
                'goods_id' => $data['goods_id']
            ]);
            if ($data['type'] == 'inc' && $isExist) {
                return '请勿重复收藏';
            } elseif ($data['type'] == 'dec' && !$isExist) {
                return '此商品未被收藏';
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function userCollect($value, $rule, $data)
    {
        try {
            if (user_info('id') == $data['other_user_id']) {
                exception('不能关注自己');
            }
            $isExist = RelationUserCollect::get([
                'user_id' => user_info('id'),
                'other_user_id' => $data['other_user_id']
            ]);
            if ($data['type'] == 'inc' && $isExist) {
                return '请勿重复关注';
            } elseif ($data['type'] == 'dec' && !$isExist) {
                return '此用户未被关注';
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
}