<?php

namespace api\controllers;

use api\extensions\ApiBaseController;
use api\services\GoodsQueryService;
use backend\models\Code;
use backend\models\Goods;
use backend\models\Icon;
use backend\models\Message;
use backend\models\SetImage;
use backend\models\TestLog;
use backend\models\UserGoods;
use common\components\Helper;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\UploadedFile;


/**
 * DefaultController controller
 */
class IndexController extends ApiBaseController
{



    /**
     * 首页
     * **/
    public function actionIndex()
    {

        $data = [
            'banner' => [],
            'icon' => [],
            'message'=>[],
        ];
        $banner = Icon::getList(['type' => 1]);
        $icon = Icon::getList(['type' => 3]);
        $message=SetImage::getList(['type'=>5]);
        foreach ($banner as $k => $v) {
            $data['banner'][] = [
                'image' => $this->setImg($v['image']),
                'href' => $v['href'],
                'category' => $v['category'],
                'appid' => $v['appid'],
            ];
        }
        foreach ($icon as $k => $v) {
            $data['icon'][] = [
                'image' => $this->setImg($v['image']),
                'href' => $v['href'],
                'title' => $v['title'],
                'subtitle' => $v['subtitle'],
                'category' => $v['category'],
                'appid' => $v['appid'],
            ];
        }

        foreach ($message as $k => $v) {
            $data['message'][] = [
                'message_id'=>$v->id,
                'title' => $v->title,
            ];
        }


        $params=[
            'hot'=>1,//首页推荐
            'page'=>Yii::$app->request->post('page', 1),
            'page_number'=>Yii::$app->request->post('page_number', 10),
        ];
        $goods = GoodsQueryService::searchModel($params);
        $data['goods']=$goods;


        return $this->jsonSuccess($data);
    }



    public function actionDetail()
    {



        $message_id=Yii::$app->request->post('message_id','');
        $model=SetImage::findOne($message_id);
        $data['detail']=[
            'title'=>$model['title'],
            'content'=>Helper::imageUrl($model['info'], Yii::$app->request->hostInfo),
            'time'=>date('Y-m-d H:i:s',$model['created_at'])
        ];


        return $this->jsonSuccess($data);
    }




    public function actionTest()
    {
        $post = Yii::$app->request->post();
        if (!$post) {
            $input = file_get_contents('php://input');
            if(!$input){
                return $this->jsonError('没有数据');
            }
            $new = new TestLog();
            $new->content = $input;
            $new->created_at = time();
            $new->type = '';
            $new->ip = Yii::$app->request->getUserIP();
            if ($new->save()) {
                $data = [
                    'message' => '提交成功',
                ];
                return $this->jsonSuccess($data);
            }else{
                return $this->jsonError('保存失败');
            }
        } else {

            $new = new TestLog();
            $new->content = json_encode($post);
            $new->created_at = time();
            $new->ip = Yii::$app->request->getUserIP();
            if ($new->save()) {
                $data = [
                    'message' => '提交成功',
                ];
                return $this->jsonSuccess($data);
            }else{
                return $this->jsonError('保存失败');
            }

        }
    }




    //发送验证码
    public function actionCode()
    {

        $params = Yii::$app->request->post();
        // 自定义验证规则
        $customRules = [];
        $rules = $this->getRules(['mobile'], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $mobile = $params['mobile'];
        $model = Code::find()->where(['phone' => $mobile])->one();
        $number = rand(10000, 99999);
        if ($model) {
            if ((time() - $model['create_time']) <= 60) {
                return $this->jsonError('短信发送太频繁，请等待1分钟');
            } else {
                $model['number'] = $number;
                $model['phone'] = "$mobile";
                $model['expire_time'] = time() + 300;
                $model['create_time'] = time();

            }

        } else {
            $model = new Code();
            $model['number'] = $number;
            $model['phone'] = "$mobile";
            $model['expire_time'] = time() + 300;
            $model['create_time'] = time();
        }

        if ($model->save()) {
            $re = Helper::sendSms2($mobile, $number);
            if (!$re) {
                return $this->jsonError('发送失败1');
            }

        } else {

            return $this->jsonError('发送失败2');

        }


        $data = [
            'message' => '短信发送成功'
        ];

        return $this->jsonSuccess($data);
    }


    //单页详情
    public function actionMessage()
    {
        $params = Yii::$app->request->post();
        // 自定义验证规则
        $customRules = [];
        $rules = $this->getRules(['type'], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }

        $model = Message::find()->where(['type' => $params['type']])->limit(1)->one();

        $data = [
            'message' => Helper::imageUrl($model->content, Yii::$app->request->hostInfo)
        ];
        return $this->jsonSuccess($data);

    }


    /**
     * 异常入口
     * **/
    public function actionError()
    {
        return $this->jsonError();
    }


    //上传图片

    public function actionUpImage()
    {
        if (!isset($_FILES['file'])) {
            return $this->jsonError('请上传数据');
        }

        $file = UploadedFile::getInstanceByName('file');

        if (!$file) {
            return ['state' => '没有选择文件'];
        }

        // 临时保存文件
        $tempPath = sys_get_temp_dir() . '/ueditor_' . uniqid() . '_' . $file->name;
        if (!$file->saveAs($tempPath)) {
            return ['state' => '临时文件保存失败'];
        }

        try {
            // 使用 CURL 调用本地接口
            $ch = curl_init();
            $postData = [
                'file' => new \CURLFile($tempPath, $file->type, $file->name)
            ];
            curl_setopt_array($ch, [
                CURLOPT_URL => Url::to(['/file/upload-images'], true),
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new \Exception("CURL 错误: {$error}");
            }
            $result = Json::decode($response);
            // 格式化响应
            if (isset($result['state']) && $result['state'] === 'success') {
                $data= [
                    'state' => 'SUCCESS',
                    'url' => $this->setImg($result['url']),
                    'title' => basename($result['url']),
                    'original' => $result['imgName'] ?? basename($result['url']),
                    'type' => '.' . pathinfo($result['url'], PATHINFO_EXTENSION),
                    'size' => $result['size'] ?? 0,
                ];
                return $this->jsonSuccess($data);
            } else {
                $url_now=Url::to(['/file/upload-images']);
                $data=[
                    'state' => $result['msg'] ?? '上传失败',
                    'url' => $url_now,
                    'title' => '',
                    'original' => '',
                    'type' => '',
                    'size' => 0,
                ];
                return $this->jsonSuccess($data);
            }

        } catch (\Exception $e) {
            return $this->jsonError('上传失败');
        } finally {
            // 清理临时文件
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }

        }
    }

}
