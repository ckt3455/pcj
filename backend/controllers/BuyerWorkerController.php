<?php

namespace backend\controllers;

use Yii;
use backend\search\BuyerWorkerSearch;
use backend\models\BuyerWorker;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

/**
 * BuyerWorkerController implements the CRUD actions for BuyerWorker model.
 */
class BuyerWorkerController extends MController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => BuyerWorker::className(),
                'data' => function(){
                    
                        $searchModel = new BuyerWorkerSearch();
                        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => BuyerWorker::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => BuyerWorker::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => BuyerWorker::className(),
            ],
        ];
    }
}
