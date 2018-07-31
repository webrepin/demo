<?php
namespace api\modules\v1\controllers\actions;

use Yii;
use yii\web\ForbiddenHttpException;

class Forbidden extends Base
{
    public function init()
    {
        throw new ForbiddenHttpException(Yii::t('api', 'Access to this resource is forbidden'));
    }
}
