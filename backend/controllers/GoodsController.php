<?php
namespace backend\controllers;


use backend\actions\DeleteAction;
use backend\actions\IndexAction;
use backend\models\Goods;
use backend\models\GoodsOption;
use backend\models\GoodsSpec;
use backend\models\SetImage;
use backend\search\GoodsSearch;
use common\components\CommonFunction;
use yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\base\Exception;

/**
 * Class ArticleController
 * @package backend\controllers
 * 文章管理控制器
 */
class GoodsController extends MController
{

    /**
     * @return array
     * 统一加载
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => Goods::className(),
                'data' => function () {
                    $searchModel = new GoodsSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];

                }
            ],

            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => GoodsSearch::className(),
            ],
            'upload' => [
                'class' => 'common\components\FixedUEditorAction',
                'config' => [
                    //图片
                    "imageUrlPrefix" => '',//图片访问路径前缀
                    "imageRoot" => Yii::getAlias("@attachment"),//根目录地址
                    'imageManagerListPath' => '/../attachment/images/',  // 对应 imagePathFormat 的父目

                    //视频
                    "videoUrlPrefix" => Yii::getAlias("@attachurl"),
                    "videoPathFormat" => "/upload/video/{yyyy}/{mm}/{dd}/{time}{rand:6}",
                    "videoRoot" => Yii::getAlias("@attachment"),
                    //文件
                    "fileUrlPrefix" => Yii::getAlias("@attachurl"),
                    "filePathFormat" => "/upload/file/{yyyy}/{mm}/{dd}/{time}{rand:6}",
                    "fileRoot" => Yii::getAlias("@attachment"),
                    'imageMaxSize' => 2048000,
                    'scrawlMaxSize' => 2048000,
                    'catcherMaxSize' => 2048000,
                    'videoMaxSize' => 102400000,
                    'fileMaxSize' => 51200000,
                    'imageAllowFiles' => ['.png', '.jpg', '.jpeg', '.gif', '.bmp'],
                    'scrawlAllowFiles' => ['.png'],
                    'videoAllowFiles' => [
                        '.flv', '.swf', '.mkv', '.avi', '.rm', '.rmvb', '.mpeg', '.mpg',
                        '.ogg', '.ogv', '.mov', '.wmv', '.mp4', '.webm', '.mp3', '.wav', '.mid'
                    ],
                    'fileAllowFiles' => [
                        '.png', '.jpg', '.jpeg', '.gif', '.bmp',
                        '.flv', '.swf', '.mkv', '.avi', '.rm', '.rmvb', '.mpeg', '.mpg',
                        '.ogg', '.ogv', '.mov', '.wmv', '.mp4', '.webm', '.mp3', '.wav', '.mid',
                        '.rar', '.zip', '.tar', '.gz', '.7z', '.bz2', '.cab', '.iso',
                        '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.pdf', '.txt', '.md', '.xml'
                    ],
                ],
            ]
        ];
    }

    /**
     * @return string|\yii\web\Response
     * 编辑/新增
     */
    public function actionCreate()
    {
        $model = new Goods();
        $model->loadDefaultValues();

        //提交表单
        if ($model->load(Yii::$app->request->post()))
        {

            $error = 0;
            $post = Yii::$app->request->post();
            if($model['has_option']==1){
                if(empty($post['title']) || empty($post['crossed_price']) || empty($post['price']) || empty($post['upc_code']) || empty($post['weight'])){
                    $error=1;
                    CommonFunction::message2('请填写规格参数', 'error');
                }
            }

            if($error==0) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save()) {
                        $error = $model->getErrors();
                        $error = reset($error);
                        throw new Exception(reset($error));
                    }


                    if ($model['has_option'] == 1) {

                        $specs_data = array();
                        foreach ($post['title'] as $k=>$v){
                            $specs_data[]=array(
                                'title'=>$v,
                                'list' => explode(',',$post['group'][$k])
                            );

                        }
                        $price_data = array();
                        foreach ($post['price'] as $k=>$v){
                            $specs = array();

                            foreach (explode('_',$k) as $k2=>$v2){
                                $specs[]=array(
                                    'title'=>$post['title'][$k2],
                                    'option'=>$v2,
                                );
                            }
                            $price_data[]=array(
                                'price'=>$v,
                                'crossed_price'=>$post['crossed_price'][$k],
                                'weight'=>$post['weight'][$k],
                                'upc_code'=>$post['upc_code'][$k],
                                'stock'=>isset($post['stock'])?$post['stock'][$k]:0,
                                'thumb'=>isset($post['thumb'])?$post['thumb'][$k]:'',
                                'specs' => $specs,
                            );

                        }

                        $gkid = GoodsOption::SetData($model['id'],$specs_data,$price_data);

                        //$gkid = IntegralGoodsOption::SetData($model['id'], $post['title'], $post['group'], $post['price'],$post['stock']);

                        if(empty($gkid)){
                            throw new Exception('添加商品规格错误');
                        }
                    }
                    $model->SetMinPrice();
                    Goods::SetStock($model->id);
                    CommonFunction::message2('保存修改成功');
                    $transaction->commit();
                    return $this->render('/layer/close');

                } catch (Exception $e) {
                    CommonFunction::message2($e->getMessage(), 'error');

                    $transaction->rollBack();
                    if(is_array($model->thumbs)){
                        $model->thumbs = serialize($model->thumbs);
                    }
                }
            }
        }
        if($model->hot and !is_array($model->hot)){
            $model->hot = explode(',',$model->hot);
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }


    public function actionUpdate()
    {
        $request  = Yii::$app->request;
        $id       = $request->get('id');
        $model    = $this->findModel($id);

        //提交表单
        if ($model->load(Yii::$app->request->post()))
        {

            $error = 0;
            $post = Yii::$app->request->post();
            if($model['has_option']==1){
                if(empty($post['title']) || empty($post['crossed_price']) || empty($post['price']) || empty($post['upc_code']) || empty($post['weight'])){
                    $error=1;
                    CommonFunction::message2('请填写规格参数', 'error');
                }
            }

            if($error==0) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save()) {
                        $error = $model->getErrors();
                        $error = reset($error);
                        throw new Exception(reset($error));
                    }


                    if ($model['has_option'] == 1) {

                        $specs_data = array();
                        foreach ($post['title'] as $k=>$v){
                            $specs_data[]=array(
                                'title'=>$v,
                                'list' => explode(',',$post['group'][$k])
                            );

                        }
                        $price_data = array();
                        foreach ($post['price'] as $k=>$v){
                            $specs = array();

                            foreach (explode('_',$k) as $k2=>$v2){
                                $specs[]=array(
                                    'title'=>$post['title'][$k2],
                                    'option'=>$v2,
                                );
                            }
                            $price_data[]=array(
                                'price'=>$v,
                                'crossed_price'=>$post['crossed_price'][$k],
                                'weight'=>$post['weight'][$k],
                                'upc_code'=>$post['upc_code'][$k],
                                'stock'=>isset($post['stock'])?$post['stock'][$k]:0,
                                'thumb'=>isset($post['thumb'])?$post['thumb'][$k]:'',
                                'specs' => $specs,
                            );

                        }

                        $gkid = GoodsOption::SetData($model['id'],$specs_data,$price_data);

                        //$gkid = IntegralGoodsOption::SetData($model['id'], $post['title'], $post['group'], $post['price'],$post['stock']);

                        if(empty($gkid)){
                            throw new Exception('添加商品规格错误');
                        }
                    }
                    $model->SetMinPrice();
                    Goods::SetStock($model->id);
                    CommonFunction::message2('保存修改成功');
                    $transaction->commit();
                    return $this->redirect(['update','id'=>$model->id]);

                } catch (Exception $e) {
                    CommonFunction::message2($e->getMessage(), 'error');

                    $transaction->rollBack();
                    if(is_array($model->thumbs)){
                        $model->thumbs = serialize($model->thumbs);
                    }
                }
            }
        }
        if($model->hot and !is_array($model->hot)){
            $model->hot = explode(',',$model->hot);
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionSellOut()
    {

        $request  = Yii::$app->request;
        $search = new GoodsSearch();
        $search->goods_status = 3;
        $data=$search->search($request->get());

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->_pageSize]);
        $models = $data->offset($pages->offset)->orderBy('sort asc,id desc')->limit($pages->limit)->all();

        return $this->render('sell_out', [
            'models' => $models,
            'pages' => $pages,
            'search' => $search,
        ]);
    }

    /**
     * 仓库中
     */
    public function actionWarehouse()
    {
        $request  = Yii::$app->request;
        $search = new GoodsSearch();
        $search->goods_status = 2;
        $data=$search->search($request->get());

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->_pageSize]);
        $models = $data->offset($pages->offset)->orderBy('sort asc,id desc')->limit($pages->limit)->all();

        return $this->render('warehouse', [
            'models' => $models,
            'pages' => $pages,
            'search' => $search,
        ]);
    }
    /**
     * 仓库中
     */
    public function actionStockWarning()
    {
        $request  = Yii::$app->request;
        $search = new GoodsSearch();
        $search->goods_status = 4;
        $data=$search->search($request->get());

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->_pageSize]);
        $models = $data->offset($pages->offset)->orderBy('sort asc,id desc')->limit($pages->limit)->all();

        return $this->render('stock_warning', [
            'models' => $models,
            'pages' => $pages,
            'search' => $search,
        ]);
    }
    /**
     * 回收站
     */
    public function actionRecycleBin()
    {
        $request  = Yii::$app->request;
        $search = new GoodsSearch();
        $search->is_del = 1;
        $search->goods_status = 5;
        $data=$search->search($request->get());

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->_pageSize]);
        $models = $data->offset($pages->offset)->orderBy('sort asc,id desc')->limit($pages->limit)->all();

        return $this->render('recycle_bin', [
            'models' => $models,
            'pages' => $pages,
            'search' => $search,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 删除
     */
    public function actionDelete($id)
    {
        if($this->findModel($id)->SetStatus(3))
        {
            $this->message("删除成功",$this->redirect(Yii::$app->request->referrer));
        }
        else
        {
            $this->message("删除失败",$this->redirect(Yii::$app->request->referrer),'error');
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 删除
     */
    public function actionDeleteAll()
    {
        $request = Yii::$app->request;
        if($request->isAjax)
        {
            $result = [];
            $result['flg'] = 2;
            $result['msg'] = "删除失败!";
            $id    = $request->post('id');
            if(!empty($id) && is_array($id)){
                foreach ($id as $k=>$v){
                    $this->findModel($v)->SetStatus(3);
                }
                $result['flg'] = 1;
                $result['msg'] = "删除成功!";
            }else{
                $result['msg'] = "请选择需要删除的数据!";
            }
            return json_encode($result);
        }
        else
        {
            throw new NotFoundHttpException('请求出错!');
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 恢复
     */
    public function actionRecover($id)
    {
        if($this->findModel($id)->SetStatus(4))
        {
            $this->message("恢复成功",$this->redirect(Yii::$app->request->referrer));
        }
        else
        {
            $this->message("恢复失败",$this->redirect(Yii::$app->request->referrer),'error');
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 恢复
     */
    public function actionRecoverAll()
    {
        $request = Yii::$app->request;
        if($request->isAjax)
        {
            $result = [];
            $result['flg'] = 2;
            $result['msg'] = "恢复失败!";

            $id    = $request->post('id');
            if(!empty($id) && is_array($id)){
                foreach ($id as $k=>$v){
                    $this->findModel($v)->SetStatus(4);
                }
                $result['flg'] = 1;
                $result['msg'] = "恢复成功!";
            }else{
                $result['msg'] = "请选择需要恢复的数据!";
            }
            return json_encode($result);
        }
        else
        {
            throw new NotFoundHttpException('请求出错!');
        }
    }
    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 下架
     */
    public function actionSoldOut($id)
    {
        if($this->findModel($id)->SetStatus(2))
        {
            $this->message("下架成功",$this->redirect(Yii::$app->request->referrer));
        }
        else
        {
            $this->message("下架失败",$this->redirect(Yii::$app->request->referrer),'error');
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 下架
     */

    public function actionSoldOutAll()
    {
        $request = Yii::$app->request;
        if($request->isAjax)
        {
            $result = [];
            $result['flg'] = 2;
            $result['msg'] = "下架失败!";

            $id    = $request->post('id');
            if(!empty($id) && is_array($id)){
                foreach ($id as $k=>$v){
                    $this->findModel($v)->SetStatus(2);
                }
                $result['flg'] = 1;
                $result['msg'] = "下架成功!";
            }else{
                $result['msg'] = "请选择需要下架的数据!";
            }
            return json_encode($result);
        }
        else
        {
            throw new NotFoundHttpException('请求出错!');
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 上架
     */
    public function actionPutaway($id)

    {
        if($this->findModel($id)->SetStatus(1))
        {
            $this->message("上架成功",$this->redirect(Yii::$app->request->referrer));
        }
        else
        {
            $this->message("上架失败",$this->redirect(Yii::$app->request->referrer),'error');
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 上架
     */
    public function actionPutawayAll()
    {
        $request = Yii::$app->request;
        if($request->isAjax)
        {
            $result = [];
            $result['flg'] = 2;
            $result['msg'] = "上架失败!";

            $id    = $request->post('id');
            if(!empty($id) && is_array($id)){
                foreach ($id as $k=>$v){
                    $this->findModel($v)->SetStatus(1);
                }
                $result['flg'] = 1;
                $result['msg'] = "上架成功!";
            }else{
                $result['msg'] = "请选择需要上架的数据!";
            }
            return json_encode($result);
        }
        else
        {
            throw new NotFoundHttpException('请求出错!');
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 彻底删除
     */
    public function actionShiftDelete($id)
    {
        if($this->findModel($id)->ShiftDelete())
        {
            $this->message("彻底删除成功",$this->redirect(Yii::$app->request->referrer));
        }
        else
        {
            $this->message("彻底删除失败",$this->redirect(Yii::$app->request->referrer),'error');
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * 彻底删除
     */
    public function actionShiftDeleteAll()
    {
        $request = Yii::$app->request;
        if($request->isAjax)
        {
            $result = [];
            $result['flg'] = 2;
            $result['msg'] = "彻底删除失败!";

            $id    = $request->post('id');
            if(!empty($id) && is_array($id)){
                foreach ($id as $k=>$v){
                    $this->findModel($v)->ShiftDelete();
                }
                $result['flg'] = 1;
                $result['msg'] = "彻底删除成功!";
            }else{
                $result['msg'] = "请选择需要彻底删除的数据!";
            }
            return json_encode($result);
        }
        else
        {
            throw new NotFoundHttpException('请求出错!');
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
            return json_encode($result);
        }
        else
        {
            throw new NotFoundHttpException('请求出错!');
        }
    }

    public function actionGetList($q=false,$notid=null)
    {
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!$q) {
            return false;
        }

        $search = new GoodsSearch();
        $searchData = array(
            //'status'=>array(1),
            'keywords'=>$q,
        );
        $data=$search->search($searchData);
        if(!empty($notid)){
            $data->andWhere(['not in','g.id',$notid]);
        }

        $models = $data->orderBy('g.sort asc,g.id desc')->select('g.id, g.title as text')->asArray()->all();

        $out['results'] = array_values($models);

        return json_encode($out);

    }
    public function actionGetSku()
    {
        $request = Yii::$app->request;
        if($request->isAjax)
        {
            $result = [];
            $result['flg'] = 2;
            $result['msg'] = "修改失败!";

            $id    = $request->get('id');
            $model = GoodsOption::find()->where(['goods_id'=>$id])->asArray()->all();

            return json_encode($model);
        }
        else
        {
            throw new NotFoundHttpException('请求出错!');
        }
    }
    /**
     * @throws NotFoundHttpException
     * 修改
     */
    public function actionGetDatas()
    {
        $request = Yii::$app->request;
        if($request->isAjax)
        {
            $result = [];
            $result['flg'] = 2;
            $result['msg'] = "获取失败!";

            $id    = $request->get('goods_id');

            $models = Goods::find()->where(['in','id',$id])->select('id,thumb,price,stock,has_option')->asArray()->all();

            foreach ($models as &$v){

                $v['option'] = GoodsOption::find()->where(['goods_id'=>$v['id']])->select('id,specs,title,price,stock')->all();

                $v['sku'] = GoodsSpec::find()->where(['goods_id'=>$v['id']])->orderBy('sort asc')->select('id,title')->asArray()->all();

            }

            $model = GoodsOption::find()->where(['goods_id'=>$id])->asArray()->all();

            return json_encode($model);
        }
        else
        {
            throw new NotFoundHttpException('请求出错!');
        }
    }

    /**
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     * 返回模型
     */
    protected function findModel($id)
    {
        if (empty($id))
        {
            $model = new Goods();
            return $model->loadDefaultValues();
        }
        $model = Goods::findOne($id);
        if (empty($model))
        {
            $model = new Goods();
            return $model->loadDefaultValues();
        }

        return $model;
    }
}