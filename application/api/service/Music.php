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

namespace app\api\service;

/**
 * 百度音乐API
 * Class Music
 * @package app\api\service
 */
class Music
{
    /**
     * 请求地址
     * @var string
     */
    protected $url = 'http://tingapi.ting.baidu.com/v1/restserver/ting?';

    /**
     * 搜索音乐
     * @param $query   string  歌名|歌手
     * @return string
     */
    public function searchMusic($query)
    {
        $this->url .= 'method=baidu.ting.search.catalogSug&query=' . $query;
        $resource = $this->send();
        if (!$resource)
            return [];
        $list['row'] = count($resource['song']);
        foreach ($resource['song'] as $k => $v) {
            $this->url .= 'method=baidu.ting.song.play&songid=' . $v['song_id'];
            $fileInfo = $this->send();
            if (!$fileInfo || empty($fileInfo['songinfo'])) {
                continue;
            }
            $list['song'][$k] = [
                'id'     => $v['songid'],
                'name'   => $v['songname'],
                'artist' => $v['artistname'],
                'link'   => $fileInfo['bitrate']['file_link'],
                'picture' => $fileInfo['songinfo']['pic_small']
            ];
        }
        return $list;
    }

    /**
     * 通过音乐id获取文件地址
     * @param $songId
     * @return bool|array
     */
    public function getMusicBySongId($songId)
    {
        $this->url .= 'method=baidu.ting.song.play&songid=' . $songId;
        $resource = $this->send();
        if (!$resource || empty($resource['songinfo']))
            return [];
        $songInfo['name']      = $resource['songinfo']['title'];
        $songInfo['author']    = $resource['songinfo']['author'];
        $songInfo['link']      = $resource['bitrate']['file_link'];
        $songInfo['picture']   = $resource['songinfo']['pic_small'];
        return $songInfo;
    }

    /**
     * 推荐音乐
     * @param int $page
     * @param int $row
     * @return array
     */
    public function getRecommendList($page = 0, $row = 10)
    {
        //推荐类型： 21-纯音乐
        $type = 21;
        $this->url .= 'method=baidu.ting.billboard.billList&type='. $type .'&size='. $row .'&offset='. $page;
        $resource = $this->send();
        if (!$resource || $resource['song_list'] == null)
            return [];
        $list['row'] = count($resource['song_list']);
        foreach ($resource['song_list'] as $k => $v) {
            if (!$resource || empty($resource['songinfo'])) {
                $this->url .= 'method=baidu.ting.song.play&songid=' . $v['song_id'];
                $fileInfo = $this->send();
                if (!$fileInfo || empty($fileInfo['songinfo'])) {
                    continue;
                }
                $list['song_list'][$k] = [
                    'id'        => $v['song_id'],
                    'name'      => $v['title'],
                    'author'    => $v['author'],
                    'picture'   => $v['pic_small'],
                    'link'      => $fileInfo['bitrate']['file_link']
                ];
            }
        }
        return $list;
    }

    protected function send()
    {
        $resource = file_get_contents($this->url);
        $resource = json_decode($resource, true);
        return  $resource['error_code'] == 22000 ? $resource : false;
    }

}