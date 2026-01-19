<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BuyerWorker */

$this->title = 'Create Buyer Worker';
$this->params['breadcrumbs'][] = ['label' => 'Buyer Workers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buyer-worker-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
