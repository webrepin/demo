<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use common\traits\FindModelTrait;

class BaseController extends \yii\rest\Controller
{
    use FindModelTrait;

    public $isAuthOptional = false;

    public function init()
    {
        parent::init();

        $auth = Yii::createObject(HttpBearerAuth::className());
        $response = Yii::$app->getResponse();
        $identity = null;
        try {
            $identity = $auth->authenticate(
                Yii::$app->getUser(),
                Yii::$app->getRequest(),
                $response
            );
        } catch (UnauthorizedHttpException $e) {
            if (!$this->isAuthOptional) {
                throw $e;
            }
        }

        if (!$identity && !$this->isAuthOptional) {
            $auth->challenge($response);
            $auth->handleFailure($response);
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'contentNegotiator' => [
                    'class' => ContentNegotiator::className(),
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON,
                    ]
                ],
                'authenticator' => [
                    'class' => HttpBearerAuth::className(),
                ]
            ]
        );
    }

}
