<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');




$configFiles = [
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../api/config/main.php'),
];
foreach (['common'] as $env) {
    $localConfig = __DIR__ . "/../../$env/config/main-local.php";
    if (file_exists($localConfig)) {
        $configFiles[] = require($localConfig);
    }
}
$config = call_user_func_array([yii\helpers\ArrayHelper::class, 'merge'], $configFiles);


$application = new yii\web\Application($config);

$application->run();
