<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BuyerLevel */

$this->title = 'Create Buyer Level';
$this->params['breadcrumbs'][] = ['label' => 'Buyer Levels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buyer-level-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
