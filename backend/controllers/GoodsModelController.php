<?php

namespace backend\controllers;

use api\extensions\ApiBaseController;
use api\services\GoodsQueryService;
use api\services\UserGoodsQueryService;
use backend\models\Goods;
use backend\models\UserGoods;
use Yii;

/**
 * DefaultController controller
 */
class GoodsModelController extends ApiBaseController
{

    /**
     * 设备列表
     **/
    public function actionList()
    {
        $params = Yii::$app->request->post();
        // 自定义验证规则
        $customRules = [];
        $rules = $this->getRules(['admin_id'], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params = Yii::$app->request->post();
        $goods=GoodsQueryService::searchModel($params);
        return $this->jsonSuccess($goods);
    }


    public function actionDetail()
    {
        $params = Yii::$app->request->post();
        // 自定义验证规则
        $customRules = [
            [['goods_model_id'], 'required', 'message' => '设备id不能为空'],
        ];
        $rules = $this->getRules(['admin_id'], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $data['detail'] =GoodsQueryService::get_one($params['goods_model_id']);;

        return $this->jsonSuccess($data);
    }


    public function actionUpdate()
    {
        $params = Yii::$app->request->post();


        // 自定义验证规则
        $customRules = [
            [['goods_model_id'], 'required', 'message' => '设备id不能为空'],
        ];
        $goods_id = YII::$app->request->post('goods_model_id');
        $rules = $this->getRules(['admin_id'], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }

        $goods = Goods::findOne($goods_id);
        $goods->setAttributes($params);
        $data=[
            'message'=>'修改成功'
        ];
        if(!$goods->save()){
            return $this->jsonError('修改失败');
        }


        return $this->jsonSuccess($data);
    }


    public function actionAdd()
    {
        $params = Yii::$app->request->post();


        // 自定义验证规则
        $customRules = [
            [['goods_code'], 'required', 'message' => '产品编号不能为空'],
        ];
        $goods_id = YII::$app->request->post('goods_model_id');
        $rules = $this->getRules(['admin_id'], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }

        $goods = new Goods();
        $goods->setAttributes($params);
        $data=[
            'message'=>'添加成功'
        ];
        if(!$goods->save()){
            return $this->jsonError('添加成功');
        }


        return $this->jsonSuccess($data);
    }




    public function actionDelete()
    {
        $params = Yii::$app->request->post();


        // 自定义验证规则
        $customRules = [
            [['goods_model_id'], 'required', 'message' => '设备id不能为空'],
        ];
        $goods_id = YII::$app->request->post('goods_model_id');
        $rules = $this->getRules(['admin_id'], $customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }

        $goods = Goods::findOne($goods_id);
        $data=[
            'message'=>'删除成功'
        ];
        if(!$goods->delete()){
            return $this->jsonError('删除失败');
        }


        return $this->jsonSuccess($data);
    }



}
