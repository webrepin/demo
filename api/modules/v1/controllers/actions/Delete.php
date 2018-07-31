<?php
namespace api\modules\v1\controllers\actions;

class Delete extends Base
{
    public function run($id)
    {
        $model = null; //$this->findModel($id);

        return $this->process($this->modelClass, [
            'id'    => $id,
            'model' => $model,
        ]);
    }
}
