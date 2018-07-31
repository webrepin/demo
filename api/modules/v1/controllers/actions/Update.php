<?php
namespace api\modules\v1\controllers\actions;

class Update extends Base
{
    public function run($id)
    {
        $model = null; //Yii::$app->user->identity;

        return $this->process($this->modelClass, [
            'id'    => $id,
            'model' => $model,
        ]);
    }
}
