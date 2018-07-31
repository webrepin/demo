<?php
namespace api\modules\v1\models\request\common;

use yii;
use common\models\AuthAccessToken;
use api\modules\v1\models\request\Base;
use api\modules\v1\models\request\Error;

class AuthRefreshToken extends Base
{
    public $access_token;
    public $expires_in;
    public $refresh_token;
    
    protected $token;

    public function getToken()
    {
        if (is_null($this->token)) {
            $this->token = AuthAccessToken::findOne(['refresh_code' => $this->refresh_token]);
        }

        return $this->token;
    }

    /**
     * Define rules for validation
     */
    public function rules()
    {
        return [
            ['refresh_token', 'required', 'message' => Yii::t('api', 'Refresh token is required')],
            ['refresh_token',  'validateRefreshToken'],
        ];
    }

    /**
     * Validates refresh token
     * @param $attribute
     * @param $params
     */
    public function validateRefreshToken($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $token = $this->getToken();
            if (!$token) {
                $this->addError($attribute, Yii::t('api', 'Invalid token'));
                $this->setErrorCode(Error::BASE, Error::BASE_BAD_TOKEN);
            }
        }
    }

    /** @inheritdoc */
    public function run()
    {
        if ($this->validate()) {
            if (($token = $this->getToken())) {

                $userId = $token->user_id;
                $token->delete();

                if (($token = AuthAccessToken::create($userId))) {
                    $this->access_token  = $token->code;
                    $this->expires_in    = $token->expires;
                    $this->refresh_token = $token->refresh_code;
                } else {
                    $this->setErrorCode(Error::BASE, Error::BASE_INTERNAL_FAILURE);
                }
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
}
