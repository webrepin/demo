<?php
namespace api\modules\v1\traits;

use api\modules\v1\Module as v1;
use yii\base\Module;

trait ModuleTrait
{
    /**
     * @return Module|v1
     */
    public function getModule()
    {
        return \Yii::$app->getModule('v1');
    }
}
