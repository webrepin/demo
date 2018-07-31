<?php
namespace api\modules\v1\controllers\actions;

use Yii;
use yii\base\Action;
use yii\db\ActiveRecordInterface;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class Base extends Action
{
    /** @var \api\modules\v1\controllers\BaseController */
    public $controller;

    /** @var string */
    public $modelClass;

    /** @var string */
    public $findModelClass;

    /** @var callable */
    public $findModel;

    /** @var callable */
    public $prepareDataProvider;

    /** @var callable */
    public $checkAccess;

    public function init()
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass must be set.');
        }
    }

    /**
     * @param $id
     * @return ActiveRecordInterface the model found
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $id, $this);
        }

        if ($this->findModelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$findModelClass must be set.');
        }

        /* @var $findModelClass ActiveRecordInterface */
        $findModelClass = $this->findModelClass;
        $keys = $findModelClass::primaryKey();
        $model = null;

        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $findModelClass::findOne(array_combine($keys, $values));
            }
        } elseif ($id !== null) {
            $model = $findModelClass::findOne($id);
        }

        return static::assertModel($model, "Object not found: $id");
    }

    public static function assertModel($model, $message = null)
    {
        if (!$model) {
            throw new NotFoundHttpException($message ? $message : 'Object not found');
        } else {
            return $model;
        }
    }

    /**
     * Process request model $modelClass with post/get params
     * returning model as array
     * @param $modelClass
     * @param array $params
     * @return array
     */
    protected function process($modelClass, array $params = [])
    {
        /** @var \api\modules\v1\models\request\Base $model */
        $model = new $modelClass($params);
        $request = Yii::$app->request;

        $input = $request->isGet ? $request->get() : $request->post();
        $model->load($input, '');

        return $model
            ->run()
            ->toArray();
    }
}

