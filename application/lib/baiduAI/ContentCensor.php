<?php
/*
* Copyright (c) 2017 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/

namespace app\lib\baiduAI;

/**
 * 内容审核
 */
class ContentCensor
{
    /**
     * 文本审核
     * @param $content  string 待审核的文本
     * @return array
     */
    public function spam($content)
    {
        return (new AipImageCensor())->antiSpam($content);
    }

    /**
     * 图像审核
     * @param $content  string   待审核的图像 （base64编码 或  urlencode 的 图像Url）
     * @param $scenes string | array 模型服务
     * @return array
     */
    public function image($content, $scenes)
    {
        return (new AipImageCensor())->imageCensorComb($content, $scenes);
    }
}
