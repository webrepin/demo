<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', __DIR__.'/../../');

require_once(YII_APP_BASE_PATH . '/vendor/autoload.php');

$kernel = AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
//    'includePaths' => [__DIR__.'/../../common'],
    'includePaths' => [YII_APP_BASE_PATH],
    'excludePaths' => [
        __DIR__,
        YII_APP_BASE_PATH . 'vendor/'
    ]
]);

//require_once(YII_APP_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php');
require_once(YII_APP_BASE_PATH . '/common/config/bootstrap.php');
require_once(__DIR__ . '/../config/bootstrap.php');