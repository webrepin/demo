<?php
namespace api\modules\v1\models\request;

use yii;

class Index extends Base
{
    public $items;
    public $filter;

    public function getItems()
    {
        if ($this->items === null) {
            $this->items = $this->getData($this->getFilter());
        }

        return $this->items;
    }

    public function setItems(array $items)
    {
        $this->items = $items;
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
                'filter' => function($model) { return $model->getFilter(); },
                'items'  => function($model) { return $model->getItems(); },
            ]
        ];
    }
}
