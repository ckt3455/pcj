<?php

namespace backend\controllers;

use Yii;
use backend\search\BuyerSearch;
use backend\models\Buyer;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

/**
 * BuyerController implements the CRUD actions for Buyer model.
 */
class BuyerController extends MController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => Buyer::className(),
                'data' => function(){
                    
                        $searchModel = new BuyerSearch();
                        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Buyer::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Buyer::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Buyer::className(),
            ],
        ];
    }
}
