<?php
namespace api\modules\v1\models\request;

use yii;

class Create extends Base
{
    public $model;
    public $item;
    public $filter;

    public function getItem()
    {
        if ($this->item === null) {
            $this->item = $this->getData($this->getFilter());
        }

        return $this->item;
    }

    public function setItem(array $item)
    {
        $this->item = $item;
    }

    public function getFilter()
    {
        if ($this->filter === null) {
            $this->filter = [];
        }

        return $this->filter;
    }

    public function setFilter(array $filter)
    {
        $this->filter = $filter;
    }

    protected function getData(array $filter = [])
    {
        return [];
    }

    /** @inheritdoc */
    protected function response()
    {
        return [
            'default' => [
                'item'  => function($model) { return $model->getItem(); },
            ]
        ];
    }
}
