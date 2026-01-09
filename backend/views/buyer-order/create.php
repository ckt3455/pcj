<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BuyerOrder */

$this->title = 'Create Buyer Order';
$this->params['breadcrumbs'][] = ['label' => 'Buyer Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buyer-order-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
