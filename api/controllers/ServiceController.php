<?php

namespace api\controllers;

use api\extensions\ApiBaseController;
use backend\models\Goods;
use backend\models\SetImage;
use backend\models\User;
use backend\models\UserWorkerOrder;
use common\components\Helper;
use Yii;

/**
 * DefaultController controller
 */
class ServiceController extends ApiBaseController
{

    public function actionQuestion()
    {

        $model=SetImage::getList(['type'=>4]);
        $data=[
            'list'=>[]
        ];
        foreach ($model as $k=>$v){
            $data['list'][]=[
                'title'=>$v->title,
                'message'=>$v->describe
            ];
        }
        return $this->jsonSuccess($data);

    }


    public function actionAdd()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['goods_id'], 'required', 'message' => '设备不能为空'],
            [['title'], 'required', 'message' => '标题不能为空'],
            [['content'], 'required', 'message' => '描述不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];


        $goods=Goods::findOne($params['goods_id']);
        if(!$goods){
            return $this->jsonError('找不到设备');
        }
        $new=new UserWorkerOrder();
        $new->user_id=$user_message['user_id'];
        $new->goods_id=$params['goods_id'];
        $new->title=$params['title'];
        $new->content=$params['content'];
        $new->image=str_replace(Yii::$app->request->hostInfo,'',$params['image']);
        $new->order_number='gd'.date('YmdHis', time()) . $new->user_id.mt_rand(1000,9999);
        if(!$new->save()){
            $error=$new->getFirstErrors();
            return $this->jsonError(reset($error));
        }
        $data=[
            'message'=>'工单提交成功,工作人员将在2-3个工作日内处理'
        ];
        return $this->jsonSuccess($data);

    }

    public function actionList()
    {

        $params = Yii::$app->request->post();
        $customRules = [];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];

        $page = $params['page'] ?? 1;
        $pageSize = $params['page_number'] ?? 10;

        // 构建查询
        $query = UserWorkerOrder::find()->where(['user_id'=>$params['user_id']]);

        // 计算分页
        $totalCount = $query->count();
        $totalPage = ceil($totalCount / $pageSize);
        $offset = ($page - 1) * $pageSize;

        // 执行查询
        $models = $query
            ->orderBy('id desc')
            ->offset($offset)
            ->limit($pageSize)
            ->all();
        $data_goods=[];
        foreach ($models as $k=>$v){
            $data_goods[]=[
                'service_id'=>$v->id,
                'title'=>$v->title,
                'status'=>$v->status,
                'time'=>date('Y-m-d H:i:s',$v->created_at),
            ];
        }
        return [
            'model' => $data_goods,
            'pagination' => [
                'total_count' => $totalCount,
                'total_page' => $totalPage,
                'current_page' => $page,
                'page_size' => $pageSize
            ]
        ];
    }


    public function actionDetail()
    {
        $params = Yii::$app->request->post();
        $customRules = [];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $model=UserWorkerOrder::findOne($params['service_id']);
        if($model and $model['user_id']==$params['user_id']){
            $image=[];
            if($model->image){
                $arr_image=explode(',',$model->image);
                foreach ($arr_image as $v){
                    $image[]=Helper::setImg($v);
                }
            }


            $data=[
                'detail'=>[
                    'id'=>$model->id,
                    'title'=>$model->title,
                    'content'=>$model->content,
                    'time'=>date('Y-m-d H:i:s',$model->created_at),
                    'status'=>$model->status,
                    'image'=>$image,
                ]
            ];
            return $this->jsonSuccess($data);

        }else{
            return $this->jsonError('工单不存在');
        }
    }





}
