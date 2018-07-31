<?php
namespace api\modules\v1\controllers\actions;

class Create extends Base
{
    public function run()
    {
        return $this->process($this->modelClass);
    }
}
