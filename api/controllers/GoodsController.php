<?php

namespace api\controllers;

use api\extensions\ApiBaseController;
use api\services\Goods2QueryService;
use api\services\GoodsQueryService;
use api\services\UserGoodsQueryService;
use api\services\UserIntLogService;
use backend\models\Address;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsOption;
use backend\models\IntGoodsCategory;
use backend\models\SetImage;
use backend\models\User;
use backend\models\UserCart;
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


    public function actionIntCategory()
    {
        $category=IntGoodsCategory::find()->orderBy('sort asc')->all();
        $data=[
            'category'=>[],
        ];
        foreach ($category as $k=>$v){
            $data['category'][]=[
                'id'=>$v['id'],
                'title'=>$v['title'],
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


    public function actionAddCart()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['goods_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];

        $goods=Goods::findOne($params['goods_id']);

        if($goods->has_option==1){
            if(!$params['sku_value']){
                return $this->jsonError('请选择规格');
            }else{
                $sku=GoodsOption::find()->where(['goods_id'=>$params['goods_id'],'specs'=>$params['sku_value']])->limit(1)->one();
                if(!$sku){
                    return $this->jsonError('规格不正确');
                }else{
                    $old=UserCart::find()->where(['goods_id'=>$params,'user_id'=>$user_message['user_id'],'sku_id'=>$sku->id])->limit(1)->one();
                    if($old){
                        $old->created_at=time();
                        $old->number++;
                        if(!$old->save()){
                            $error=$old->getFirstErrors();
                            return $this->jsonError(reset($error));
                        }
                    }else{
                        $new=new UserCart();
                        $new->goods_id=$params['goods_id'];
                        $new->number=1;
                        $new->user_id=$params['user_id'];
                        $new->created_at=time();
                        $new->sku_id=$sku->id;
                        if(!$new->save()){
                            $error=$new->getFirstErrors();
                            return $this->jsonError(reset($error));
                        }
                    }

                }
            }
        }else{
            $old=UserCart::find()->where(['goods_id'=>$params,'user_id'=>$user_message['user_id']])->limit(1)->one();
            if($old){
                $old->created_at=time();
                $old->number++;
                if(!$old->save()){
                    $error=$old->getFirstErrors();
                    return $this->jsonError(reset($error));
                }
            }else{
                $new=new UserCart();
                $new->goods_id=$params['goods_id'];
                $new->number=1;
                $new->user_id=$params['user_id'];
                $new->created_at=time();
                if(!$new->save()){
                    $error=$new->getFirstErrors();
                    return $this->jsonError(reset($error));
                }
            }
        }
        $data = [
            'message'=>'加入购物车成功'
        ];
        return $this->jsonSuccess($data);

    }





}
