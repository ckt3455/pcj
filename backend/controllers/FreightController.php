<?php

namespace backend\controllers;

use backend\models\FreightModel;
use common\components\CommonFunction;
use Yii;
use backend\models\Freight;
use backend\search\FreightSearch;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FreightController implements the CRUD actions for Freight model.
 */
class FreightController extends MController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Freight models.
     * @return mixed
     */
    public function actionIndex()
    {

        $data= FreightModel::find();

        $pages  = new Pagination(['totalCount' =>$data->count(), 'pageSize' =>$this->_pageSize]);
        $models = $data->offset($pages->offset)->orderBy('sort asc,id desc')->limit($pages->limit)->all();

        return $this->render('index',[
            'models'  => $models,
            'pages'   => $pages,
        ]);
    }

    /**
     * Displays a single Freight model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Freight model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $model=new FreightModel();
            $model->setAttributes($post);

            if($model->save()){
                if(isset($post['citys'])){
                    foreach ($post['citys'] as $k=>$v){
                        $detail=new Freight();
                        $detail->model_id=$model->id;
                        $detail->city_id=$v;
                        $detail->first=$post['firstweight'][$k];
                        $detail->first_money=$post['firstprice'][$k];
                        $detail->next=$post['secondweight'][$k];
                        $detail->next_money=$post['secondprice'][$k];
                        if(!$detail->save()){
                            print_r($detail->getErrors());exit;
                        }
                    }
                }
                CommonFunction::message2('新增成功');
                return $this->redirect(['index']);
            }else{
                $error = $model->getErrors();
                $error = reset($error);
                $error = reset($error);
                CommonFunction::message2($error, 'error');
            }
        }

        return $this->render('create');

    }

    /**
     * Updates an existing Freight model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {


        $model = $this->findModel($id);

        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $model->setAttributes($post);
            if($model->save()){
                //先删除原先的数据
                if(isset($model->detail)){
                    Freight::deleteAll(['model_id'=>$model->id]);
                }
                if(isset($post['citys'])){
                    foreach ($post['citys'] as $k=>$v){
                        $detail=new Freight();
                        $detail->model_id=$model->id;
                        $detail->city_id=$v;
                        $detail->first=$post['firstweight'][$k];
                        $detail->first_money=$post['firstprice'][$k];
                        $detail->next=$post['secondweight'][$k];
                        $detail->next_money=$post['secondprice'][$k];
                        if(!$detail->save()){
                            print_r($detail->getErrors());exit;
                        }
                    }
                }
                CommonFunction::message2('保存成功');
                return $this->redirect(['index']);
            }else{
                $error = $model->getErrors();
                $error = reset($error);
                $error = reset($error);
                CommonFunction::message2($error, 'error');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing Freight model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(!in_array($id,array(2,4))){
            $delete = $this->findModel($id)->delete();

            if($delete)
            {
                $this->message('删除成功',$this->redirect(['index']));
            }
            else
            {
                $this->message('删除失败',$this->redirect(['index']),'error');
            }

        }else{
            $this->message('快递物流模板禁止删除',$this->redirect(['index']),'error');
        }
    }

    /**
     * @throws NotFoundHttpException
     * 修改
     */
    public function actionUpdateAjax()
    {
        $request = Yii::$app->request;
        if($request->isAjax)
        {
            $result = [];
            $result['flg'] = 2;
            $result['msg'] = "修改失败!";

            $id    = $request->get('id');
            $model = $this->findModel($id);
            $model->attributes = $request->get();
            if($model->validate() && $model->save())
            {
                $result['flg'] = 1;
                $result['msg'] = "修改成功!";
            }

            echo json_encode($result);
        }
        else
        {
            throw new NotFoundHttpException('请求出错!');
        }
    }

    /**
     * Finds the Freight model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Freight the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FreightModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
