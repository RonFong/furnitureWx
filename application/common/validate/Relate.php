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

class Relate extends BaseValidate
{
    protected $rule = [
        'article_id'    => 'require|number',
        'type'          => 'require|in:inc,dec',
        'user_id'       => 'require|number',
        'comment_id'    => 'require|number',
        'factory_id'    => 'require|number',
        'shop_id'       => 'require|number',
        'goods_id'      => 'require|number',
    ];

    protected $scene = [
        'articleCollect' => [
            'article_id',
            'type',
            'user_id'       => 'require|number|articleCollect',
        ],
        'articleGreat' => [
            'article_id',
            'type',
            'user_id'       => 'require|number|articleGreat',
        ],
        'commentGreat' => [
            'comment_id',
            'type',
            'user_id'       => 'require|number|commentGreat',
        ],
        'shopCollect' => [
            'shop_id',
            'type',
            'user_id'       => 'require|number|shopCollect',
        ],
        'factoryCollect' => [
            'factory_id',
            'type',
            'shop_id'       => 'require|number|factoryCollect',
        ],
        'goodsCollect' => [
            'goods_id',
            'type',
            'user_id'       => 'require|number|goodsCollect',

        ]
    ];

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
                'user_id' => $data['user_id'],
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
                'user_id' => $data['user_id'],
                'article_id' => $data['article_id']
            ]);
            if ($data['type'] == 'inc' && $isExist) {
                return '请勿重复点赞';
            } elseif ($data['type'] == 'dec' && !$isExist) {
                return '此文章未被点赞';
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
                'user_id' => $data['user_id'],
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
    protected function factoryCollect($value, $rule, $data)
    {
        try {
            $isExist =  RelationFactoryCollect::get([
                'shop_id' => $data['shop_id'],
                'factory_id' => $data['factory_id']
            ]);
            if ($data['type'] == 'inc' && $isExist) {
                return '请勿重复收藏';
            } elseif ($data['type'] == 'dec' && !$isExist) {
                return '此厂家未被收藏';
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
                'user_id' => $data['user_id'],
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
    protected function shopCollect($value, $rule, $data)
    {
        try {
            $isExist = RelationShopCollect::get([
                'user_id' => $data['user_id'],
                'shop_id' => $data['shop_id']
            ]);
            if ($data['type'] == 'inc' && $isExist) {
                return '请勿重复收藏';
            } elseif ($data['type'] == 'dec' && !$isExist) {
                return '此商家未被收藏';
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
}