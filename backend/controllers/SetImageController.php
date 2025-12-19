<?php

namespace backend\controllers;

use backend\models\News;
use backend\models\SinglePage;
use common\components\Helper;
use OSS\OssClient;
use Yii;
use backend\search\SetImageSearch;
use backend\models\SetImage;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

use yii\web\UploadedFile;

/**
 * SetImageController implements the CRUD actions for SetImage model.
 */
class SetImageController extends MController
{
    public function actions()
    {

        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => SetImage::className(),
                'data' => function(){
                    
                        $searchModel = new SetImageSearch();
                        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],

            'index3' => [
                'class' => IndexAction::className(),
                'modelClass' => SetImage::className(),
                'data' => function(){

                    $searchModel = new SetImageSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];

                }
            ],
            'index2' => [
                'class' => IndexAction::className(),
                'modelClass' => SetImage::className(),
                'data' => function(){

                    $searchModel = new SetImageSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];

                }
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => SetImage::className(),
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

    public function actionCreate(){
        ini_set('memory_limit', '2048M');
        $model=new SetImage();
        $model->loadDefaultValues();
            if (Yii::$app->getRequest()->getIsPost()) {


                if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {


                    $model->file = UploadedFile::getInstance($model, "file");
                    if(isset($model->file->baseName)){
                        $size=ceil($model->file->size/1024/1024*1000)/1000;
                        $name='/uploads/' .date('Ymdhis'). Helper::random(6) . '.' . $model->file->extension;
                        $name2='..' .$name;
                        $model->file->saveAs($name2);
                        $model->file_value=$name;
                        $model->up_time=time();
                        $model->file_size=$size;
                        $model->save();
                    }

                    if( yii::$app->getRequest()->getIsAjax() ){
                        return [];
                    }else {
                        return $this->render('/layer/close');
                    }
                } else {
                    $errors = $model->getErrors();
                    $err = '';
                    foreach ($errors as $v) {
                        $err .= $v[0] . '<br>';
                    }
                    yii::$app->getSession()->setFlash('error', $err);
                }
            }
            return $this->render('create', ['model'=>$model]);
    }
    public function actionUpdate(){
        ini_set('memory_limit', '2048M');
        $model=SetImage::findOne(Yii::$app->request->get('id'));
        if($model){
            if (Yii::$app->getRequest()->getIsPost()) {
                if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {

                    $model->file = UploadedFile::getInstance($model, "file");
                    if(isset($model->file->baseName)){
                        $size=ceil($model->file->size/1024/1024*1000)/1000;
                        $name='/uploads/' .date('Ymdhis'). $model->file->baseName . '.' . $model->file->extension;
                        $name2='..' .$name;
                        $model->file->saveAs($name2);
                        $model->file_value=$name;
                        $model->up_time=time();
                        $model->file_size=$size;
                        $model->save();
                    }
                    if( yii::$app->getRequest()->getIsAjax() ){
                        return [];
                    }else {
                        return $this->render('/layer/close');
                    }
                } else {
                    $errors = $model->getErrors();
                    $err = '';
                    foreach ($errors as $v) {
                        $err .= $v[0] . '<br>';
                    }
                    yii::$app->getSession()->setFlash('error', $err);
                }
            }
            return $this->render('update', ['model'=>$model]);
        }
    }






    public function actionSingle(){
        ini_set('memory_limit', '2048M');
        $model=SetImage::getOne(['type'=>Yii::$app->request->get('type'),'language'=>Yii::$app->language]);
        if($model){
            if (yii::$app->getRequest()->getIsPost()) {

                if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
                    $model->file = UploadedFile::getInstance($model, "file");
                    if(isset($model->file->baseName)){
                        $size=ceil($model->file->size/1024/1024*1000)/1000;
                        $name='/uploads/' .date('Ymdhis'). $model->file->baseName . '.' . $model->file->extension;
                        $name2='..' .$name;
                        $model->file->saveAs($name2);
                        $model->file_value=$name;
                        $model->up_time=time();
                        $model->file_size=$size;
                        $model->save();
                    }
                    if( yii::$app->getRequest()->getIsAjax() ){
                        return [];
                    }else {
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                } else {
                    $errors = $model->getErrors();
                    $err = '';
                    foreach ($errors as $v) {
                        $err .= $v[0] . '<br>';
                    }
                    yii::$app->getSession()->setFlash('error', $err);
                }
            }
            return $this->render('single', ['model'=>$model]);
        }else{
            $new=new SetImage();
            $new->type=Yii::$app->request->get('type');
            $new->language=Yii::$app->language;
            $new->save();
            $model=SetImage::getOne(['type'=>Yii::$app->request->get('type'),'language'=>Yii::$app->language]);
            return $this->render('single', ['model'=>$model]);

        }
    }







}
