<?php

namespace api\controllers;

use api\extensions\ApiBaseController;
use api\services\Goods2QueryService;
use api\services\GoodsQueryService;
use api\services\UserGoodsQueryService;
use backend\models\Address;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsOption;
use backend\models\SetImage;
use backend\models\User;
use backend\models\UserGoods;
use common\components\Helper;
use Yii;

/**
 * DefaultController controller
 */
class GoodsController extends ApiBaseController
{

    /**
     * 列表
     **/
    public function actionList()
    {
        $params = Yii::$app->request->post();
        $goods = GoodsQueryService::searchModel($params);
        return $this->jsonSuccess($goods);
    }


    public function actionList2()
    {
        $params = Yii::$app->request->post();
        $goods = Goods2QueryService::searchModel($params);
        return $this->jsonSuccess($goods);
    }


    public function actionCategory()
    {
        $category=GoodsCategory::find()->where(['parent_id'=>0])->asArray()->all();
        $banner=SetImage::getList(['type'=>2]);
        $data=[
            'category'=>[],
            'banner'=>[],
        ];
        foreach ($banner as $k=>$v){
            $data['banner'][]=[
                'id'=>$v['id'],
                'title'=>$v['title'],
                'image'=>Helper::setImg($v['image']),
            ];
        }
        foreach ($category as $k=>$v){
            $children=GoodsCategory::find()->where(['parent_id'=>$v['id']])->asArray()->all();
            $children_data=[];
            foreach ($children as $k1=>$v1){
                $children_data[]=[
                    'id'=>$v1['id'],
                    'title'=>$v1['title'],
                    'image'=>Helper::setImg($v1['image']),
                ];
            }
            $data['category'][]=[
                'id'=>$v['id'],
                'title'=>$v['title'],
                'image'=>Helper::setImg($v['image']),
                'children'=>$children_data,
            ];
        }
        return $this->jsonSuccess($data);

    }






    public function actionDetail()
    {
        $params = Yii::$app->request->post();

        // 自定义验证规则
        $customRules = [
            [['goods_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules([], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $data['detail'] =GoodsQueryService::get_one($params['goods_id']);
        return $this->jsonSuccess($data);
    }



    public function actionSkuDetail()
    {
        $params = Yii::$app->request->post();

        // 自定义验证规则
        $customRules = [
            [['goods_id'], 'required', 'message' => 'id不能为空'],
            [['sku_value'], 'required', 'message' => 'sku_value不能为空'],
        ];
        $rules = $this->getRules([], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $model=GoodsOption::find()->where(['goods_id'=>$params['goods_id'],'specs'=>$params['sku_value']])->limit(1)->one();
        if($model){
            if($model->thumb){
                $image=Helper::setImg($model->thumb);
            }else{
                $goods=Goods::findOne($params['goods_id']);
                $image=Helper::setImg($goods->thumb);
            }
            $data=[
                'goods_id'=>$model->goods_id,
                'image'=>$image,
                'title'=>$model->title,
                'price'=>$model->price,
                'crossed_price'=>$model->crossed_price,
                'stock'=>$model->stock,
                'weight'=>$model->weight,
            ];
        }else{
            $data=[];
        }

        return $this->jsonSuccess($data);
    }


    public function actionUpdate()
    {
        $params = Yii::$app->request->post();
        $goods_id = YII::$app->request->post('goods_id');

        // 自定义验证规则
        $customRules = [
            [['goods_id'], 'required', 'message' => '设备id不能为空'],
        ];
        $rules = $this->getRules(['user_id'], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }

        $goods = UserGoods::findOne($goods_id);
        if(Yii::$app->request->post('is_index') !== null){
            if(Yii::$app->request->post('is_index')==1){
                $goods->is_index = 1;
            }else{
                $goods->is_index = 0;
            }
        }

        if(Yii::$app->request->post('lx_alert') !== null){
            if(Yii::$app->request->post('lx_alert')==1){
                $goods->lx_alert = 1;
            }else{
                $goods->lx_alert = 0;
            }
        }

        if(Yii::$app->request->post('lx_reset') !== null){
            if(Yii::$app->request->post('lx_reset')==1){
                $goods->lx_end_time = time()+$goods->lx_day*24*3600;
            }
        }
        $data=[
            'message'=>'修改成功'
        ];
        if(!$goods->save()){
            return $this->jsonError('修改失败');
        }


        return $this->jsonSuccess($data);
    }


}
