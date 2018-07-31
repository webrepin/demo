<?php
namespace api\modules\v1\controllers;

use yii;

/**
 * Auth Controller
 */
class AuthController extends BaseController
{
    public $isAuthOptional = true;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        return $behaviors;
    }

    public function verbs()
    {
        return [
            'register'  => ['POST'],
            'authorize' => ['POST'],
            'refresh'   => ['POST'],
            'password'  => ['POST'],
            'license'   => ['POST'],
        ];
    }

    public function actions()
    {
        return [
            'register'  => [
                'class'      => 'api\modules\v1\controllers\actions\Create',
                'modelClass' => 'api\modules\v1\models\request\common\AuthRegister',
            ],
            'auth'  => [
                'class'      => 'api\modules\v1\controllers\actions\Create',
                'modelClass' => 'api\modules\v1\models\request\common\AuthAccessToken',
            ],
            'refresh'  => [
                'class'      => 'api\modules\v1\controllers\actions\Create',
                'modelClass' => 'api\modules\v1\models\request\common\AuthRefreshToken',
            ],
            'password' => [
                'class'      => 'api\modules\v1\controllers\actions\Create',
                'modelClass' => 'api\modules\v1\models\request\common\AuthPassword',
            ],
            'license'  => [
                'class'      => 'api\modules\v1\controllers\actions\Create',
                'modelClass' => 'api\modules\v1\models\request\common\AuthLicense',
            ],
        ];
    }
}
