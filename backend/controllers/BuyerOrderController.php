<?php

namespace backend\controllers;

use Yii;
use backend\search\BuyerOrderSearch;
use backend\models\BuyerOrder;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

/**
 * BuyerOrderController implements the CRUD actions for BuyerOrder model.
 */
class BuyerOrderController extends MController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => BuyerOrder::className(),
                'data' => function(){
                    
                        $searchModel = new BuyerOrderSearch();
                        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => BuyerOrder::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => BuyerOrder::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => BuyerOrder::className(),
            ],
        ];
    }
}
