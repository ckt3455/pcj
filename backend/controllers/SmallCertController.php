<?php

namespace backend\controllers;

use Yii;
use backend\search\SmallCertSearch;
use backend\models\SmallCert;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

/**
 * SmallCertController implements the CRUD actions for SmallCert model.
 */
class SmallCertController extends MController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => SmallCert::className(),
                'data' => function(){
                    
                        $searchModel = new SmallCertSearch();
                        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => SmallCert::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => SmallCert::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => SmallCert::className(),
            ],
        ];
    }
}
