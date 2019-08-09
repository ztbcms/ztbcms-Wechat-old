<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-09
 * Time: 10:07.
 */

namespace Wechat\Service;


use EasyWeChat\Factory;
use System\Service\BaseService;
use Wechat\Model\OfficesModel;
use Think\Exception;

class OfficeService extends BaseService
{
    public $app = null;
    protected $app_id = null;


    function __construct($app_id)
    {
        //获取授权小程序资料
        $officeModel = new OfficesModel();
        $office = $officeModel->where(['app_id' => $app_id, 'account_type' => OfficesModel::ACCOUNT_TYPE_OFFICE])->find();
        if ($office) {
            $config = [
                'app_id'        => $office['app_id'],
                'secret'        => $office['secret'],

                // 下面为可选项
                // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
                'response_type' => 'array',

                'log' => [
                    'level' => 'debug',
                    'file'  => __DIR__.'/wechat.log',
                ],
            ];
            $this->app_id = $app_id;
            $this->app = Factory::officialAccount($config);
        } else {
            throw new Exception("找不到该小程序信息");
        }
    }
}