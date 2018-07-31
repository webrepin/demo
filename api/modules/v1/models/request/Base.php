<?php
namespace api\modules\v1\models\request;

use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\request\Error;
use api\modules\v1\traits\ModuleTrait;

/**
 * Base Request Model
 */
class Base extends Model
{
    use ModuleTrait;

    protected $error_base;
    protected $error_code;
    protected $error_message;

    /**
     * Does the request ending with success / errors
     * @return boolean
     */
    public function isSuccess()
    {
        return !$this->hasErrors() && !$this->hasErrorCode();
    }

    /**
     * @return boolean
     */
    public function hasErrorCode()
    {
        return ($this->error_base !== null && $this->error_code !== null);
    }

    public function checkErrorCode($base, $code = 0)
    {
        return $this->getErrorCode() === ($base + $code);
    }

    /**
     * @param integer $base
     * @param integer $code
     */
    public function setErrorCode($base, $code = 0)
    {
        $this->error_base = $base;
        $this->error_code = $code;
        $this->error_message = null;
    }

    /**
     * @return integer
     */
    public function getErrorCode()
    {
        return $this->hasErrorCode() ? $this->error_base + $this->error_code : 0;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        if ($this->error_message === null) {
            $this->error_message = $this->hasErrorCode()
                ? Error::message($this->error_base, $this->error_code)
                : '';
        }

        return $this->error_message;
    }

    /**
     * @param string $message
     */
    public function setErrorMessage($message)
    {
        $this->error_message = $message;
    }

    /**
     * The fields of request model are response body
     * @inheritdoc
     */
    public function fields()
    {
        $fields['success'] = function($model) { return (int)$model->isSuccess(); };

        if ($this->isSuccess()) {
            $response = $this->response();
            $response = isset($response[$this->scenario]) ? $response[$this->scenario] : [];
        } else {
            $response = $this->responseError();
        }

        return ArrayHelper::merge($fields, $response);
    }

    public function afterValidate()
    {
        parent::afterValidate();
        if (!$this->isSuccess() && !$this->hasErrorCode()) {
            $this->setErrorCode(Error::BASE, Error::BASE_BAD_REQUEST);
        }
    }

    public function run()
    {
        if ($this->validate()) {
            // Do some actions here...
        }

        return $this;
    }

    /**
     * Array for fields method, where key is scenario name
     * and value is array with rules for fields method
     *
     * @see Model::fields()
     * @return array
     */
    protected function response()
    {
        return ['default' => []];
    }

    /**
     * Return fields for error response.
     *
     * @see Model::fields()
     * @return array
     */
    protected function responseError()
    {
        return [
            'error_code'    => function($model) { return $model->getErrorCode(); },
            'error_message' => function($model) { return $model->getErrorMessage(); },
            'error_log'     => function($model) { return $model->getFirstErrors(); },
        ];
    }
}
