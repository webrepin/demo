<?php
namespace api\modules\v1\controllers\actions;

use Yii;

class Index extends Base
{
    public function run()
    {
        return $this->process($this->modelClass);
    }
}
