<?php
namespace api\modules\v1\models\request\common;

use yii;
use common\models\User;
use common\models\AuthAccessToken as AccessToken;
use api\modules\v1\models\request\Base;
use api\modules\v1\models\request\Error;

class AuthAccessToken extends Base
{
    const LIFE_TIME = 3600;

    public $login;
    public $pass;
    public $access_token;
    public $expires_in;
    public $refresh_token;

    /** @var  \common\models\User */
    protected $user;

    /** @var string */
    protected $license_url;

    /**
     * @return null|\common\models\User
     */
    public function getUser()
    {
        if (!$this->user) {
            $this->user = User::findByUsername($this->login);
        }

        return $this->user;
    }

    public function getLicenseUrl()
    {
        return isset(Yii::$app->params['licenseUrl']) ? Yii::$app->params['licenseUrl'] : null;
    }

    /**
     * Define rules for validation
     */
    public function rules()
    {
        return [
            ['login', 'required', 'message' => Yii::t('api', 'Login field is required')],
            ['pass',  'required', 'message' => Yii::t('api', 'Password field is required')],
            ['login', 'validateLogin'],
            ['pass', 'validatePassword'],
        ];
    }
    
    /**
     * Validates login and password
     * @param $attribute
     * @param $params
     */
    public function validateLogin($attribute, $params)
    {
        if (!$this->hasErrors()) {
            
            $user = $this->getUser();

            if (!$user) {
                $this->addError($attribute, Yii::t('api', 'Invalid login'));
                $this->setErrorCode(Error::AUTH, Error::AUTH_BAD_CREDENTIALS);
            }
        }
    }

    /**
     * Validates login and password
     * @param $attribute
     * @param $params
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $user = $this->getUser();

            if ($user && !$user->validatePassword($this->$attribute)) {
                $this->addError($attribute, Yii::t('api', 'Invalid password'));
                $this->setErrorCode(Error::AUTH, Error::AUTH_BAD_CREDENTIALS);
            }
        }
    }

    /** @inheritdoc */
    public function run()
    {
        if ($this->validate()) {

            $user = $this->getUser();

            if (($token = AccessToken::create($user->id))) {
                $this->access_token  = $token->code;
                $this->expires_in    = $token->expires;
                $this->refresh_token = $token->refresh_code;
            } else {
                $this->setErrorCode(Error::BASE, Error::BASE_INTERNAL_FAILURE);
            }

        }

        return $this;
    }

    /** @inheritdoc */
    protected function response()
    {
        return [
            'default' => [
                'access_token'  => 'access_token',
                'expires_in'    => 'expires_in',
                'refresh_token' => 'refresh_token',
            ]
        ];
    }

    protected function responseError()
    {
        $response = parent::responseError();

        if ($this->checkErrorCode(Error::AUTH, Error::AUTH_LICENSE_REQUIRED)) {
            $response['license_url'] = 'license_url';
        }

        return $response;
    }
}
