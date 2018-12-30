<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/2/21
// +----------------------------------------------------------------------

namespace app\common\model;

use app\common\validate\BaseValidate;
use think\File;
use think\Model as CoreModel;
use think\Request;

abstract class Model extends CoreModel
{
    protected $autoWriteTimestamp = true;
    protected $autoSave = true;
    //save方法原始数据
    protected $saveData = [];
    protected $deleteTime = null;

    protected $response;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $db = $this->db(false);
        if (in_array('create_time',$db->getTableInfo('', 'fields'))) {
            $this->createTime = 'create_time';
        } else {
            $this->createTime = false;
        }
        if (in_array('update_time',$db->getTableInfo('', 'fields'))) {
            $this->updateTime = 'update_time';
        } else {
            $this->updateTime = false;
        }
        if (in_array('create_by',$db->getTableInfo('', 'fields')) && in_array('update_by', $db->getTableInfo('', 'fields'))) {
            array_push($this->insert,'create_by','update_by');
        }
        if (in_array('update_by',$db->getTableInfo('', 'fields'))) {
            array_push($this->update,'update_by');
        }
        $this->response = new BaseValidate();
    }

    public function setCreateByAttr()
    {
        return user_info('id');
    }

    public function setCreateTimeAttr()
    {
        return time();
    }

    public function setUpdateByAttr()
    {
        return user_info('id');
    }

    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getUpdateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getCreateByAttr($value)
    {
        return db('user')->where('id', $value)->value('user_name');

    }

    public function getUpdateByAttr($value)
    {
        return db('user')->where('id', $value)->value('user_name');
    }


    /**
     * 格式化时间戳输出
     * @param $value
     * @return false|string
     */
    protected function getLoginLastTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }


    public function scopeState($query)
    {
        return $query->where('state', 1);
    }

    /**
     * 判断是否带有主键数据
     * @param $data
     * @return bool
     * @throws \think\exception\DbException
     */
    private function hasPk($data)
    {
        $find = $this->get($this->checkPkData($data));
        return empty($find) ? false : true;
    }

    /**
     * 得到主键数据
     * @param $data
     * @return array|int
     */
    private function checkPkData($data)
    {
        $pk = $this->getPk();
        if(is_array($pk) && count(array_intersect_key($data, array_flip($pk))) === count($pk))
        {
            return array_intersect_key($data, array_flip($pk));
        }
        elseif (is_string($pk))
        {
            return isset($data[$pk]) ? $data[$pk] : 0;
        }
        else
        {
            \think\Log::error($this->getTable().'未找到主键数据:'.json_encode($data));
            throw new \think\exception\HttpException(500, '请求数据异常');
        }
    }

    /**
     * 是否自动判断数据库新增或修改
     * @param bool $status
     * @return $this
     */
    public function autoSave($status = true)
    {
        $this->autoSave = $status;
        return $this;
    }

    /**
     * 保存当前数据对象 字段过滤
     * @param array $data       数据
     * @param array $where      更新条件
     * @param null $sequence    自增序列名
     * @return false|int
     * @throws \think\exception\DbException
     */
    public function save($data = [], $where = [], $sequence = null)
    {
        $data = $data ?: $this->getData();
        $this->saveData = $data;
        $this->allowField(true);
        if($this->autoSave)
        {
            $this->isUpdate = $this->hasPk($data);
        }
        return parent::save($data,$where,$sequence);
    }

    /**
     * @param $file 表单字段名
     * @param string $path 子文件夹名称,为空则存储根目录
     * @param string $ext  限定文件扩展名
     * @param string $type 文件类型,image|file
     * @return bool|string
     */
    public function uploadImg($file,$path='',$ext='jpg,jpeg,png,bmp,gif',$type='image')
    {
        $fileClass = Request::instance()->file($file);
        if($fileClass) {
            if(is_array($fileClass)) {
                foreach ($fileClass as $item) {
                    $filePath[] = $this->_upload($item,$path,$ext,$type);
                }
                return $filePath;
            } else {
                return $this->_upload($fileClass,$path,$ext,$type);
            }
        }
        return false;
    }

    private function _upload(File $fileClass,$path,$ext,$type)
    {
        $save_path = $type == 'image' ? IMAGE_PATH : FILE_PATH;
        $view_save_path = $type == 'image' ? VIEW_IMAGE_PATH : VIEW_FILE_PATH;
        if($path) {
            $info = $fileClass->validate(['ext'=>$ext])->move($save_path.DS.$path,md5_file($fileClass->getInfo('tmp_name')));
            if ($info) {
                return $view_save_path.'/'.$path.'/'.$info->getSaveName();
            }
        } else {
            $info = $fileClass->validate(['ext'=>$ext])->move($save_path);
            if ($info) {
                return $view_save_path.'/'.$info->getSaveName();
            }
        }
    }

    /**
     * 获取当前实例化后的模型对应的表名
     * @return bool|string
     */
    public function getTableName()
    {
        return $this->name;
    }


    /**
     * Emoji原形转换为String
     * @param string $content
     * @return string
     */
    public function emojiEncode($content)
    {
        return json_decode(preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str) {
            return addslashes($str[0]);
        }, json_encode($content)));
    }

    /**
     * Emoji字符串转换为原形
     * @param string $content
     * @return string
     */
    public function emojiDecode($content)
    {
        return json_decode(preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, json_encode($content)));
    }

    /**
     * Emoji 表情符
     * @param $value
     * @return string
     */
    public function getContentAttr($value)
    {
        return $this->emojiDecode($value);
    }

    public function setContentAttr($value)
    {
        return $this->emojiEncode($value);
    }

    /**
     * Emoji 表情符
     * @param $value
     * @return string
     */
    public function getTextAttr($value)
    {
        return $this->emojiDecode($value);
    }

    public function setTextAttr($value)
    {
        return $this->emojiEncode($value);
    }

    public function getTitleAttr($value)
    {
        return $this->emojiDecode($value);
    }

    public function setTitleAttr($value)
    {
        return $this->emojiEncode($value);
    }
}