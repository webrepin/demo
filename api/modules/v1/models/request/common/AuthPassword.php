<?php
namespace api\modules\v1\models\request\common;

use yii;
use dektrium\user\helpers\Password;
use common\models\User;
use api\common\components\Mailer;
use api\modules\v1\models\request\Base;
use api\modules\v1\models\request\Error;

class AuthPassword extends Base
{
    public $access_token;
    public $login;

    /* @var \common\models\User */
    protected $user;

    /** @var Mailer */
    protected $mailer;

    public function getMailer()
    {
        if (is_null($this->mailer)) {
            $this->mailer = new Mailer();
        }

        return $this->mailer;
    }

    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return null|\common\models\User
     */
    public function getUser()
    {
        if (!$this->user) {
            $this->user = User::findByEmail($this->login);
            if (!$this->user) {
                $this->user = User::findByUsername($this->login);
            }
        }

        return $this->user;
    }

    /**
     * Define rules for validation
     */
    public function rules()
    {
        return [
            ['login', 'required'],
            ['login', 'validateLogin'],
        ];
    }

    /**
     * Validates login
     * @param $attribute
     * @param $params
     */
    public function validateLogin($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $user = $this->getUser();

            if (!$user) {
                $this->addError($attribute, Yii::t('api', 'Unknown login'));
                $this->setErrorCode(Error::AUTH, Error::AUTH_BAD_CREDENTIALS);
            }
        }
    }

    /** @inheritdoc */
    public function run()
    {
        if ($this->validate()) {
            if (($user = $this->getUser())) {

                $password = Password::generate(8);
                $success = $user->resetPassword($password);

                if ($success) {
                    $mailer = $this->getMailer();
                    if (!$mailer->sendPasswordResetMessage($user, $password)) {
                        $this->setErrorCode(Error::AUTH, Error::AUTH_PASSWORD_MAIL_FAILURE);
                    }
                } else {
                    $this->setErrorCode(Error::AUTH, Error::AUTH_PASSWORD_RESET_FAILURE);
                }
            } else {
                $this->setErrorCode(Error::BASE, Error::BASE_INTERNAL_FAILURE);
            }
        }

        return $this;
    }
}
