<?php
namespace api\test\unit\models\versions\v4;

use api\modules\v1\models\request\common\AuthAccessToken;
use api\modules\v1\models\request\common\AuthRegister;
use AspectMock\Test;
use Codeception\TestCase\Test as TestCase;
use common\models\User;

class AuthRegisterTest extends TestCase
{

    /**
     * @param $post
     *
     * @return AuthRegister
     */
    public function getFilledModel($post)
    {
        $form = new AuthRegister();
        $form->load($post);
        return $form;
    }

    public function testNoLogin()
    {
        $model = $this->getFilledModel([
            'email' => 'test@test.te',
            'pass' => 113,
        ]);
        $this->assertFalse($model->validate('login'));
        $this->assertTrue($model->hasErrors('login'));
        $this->assertTrue($model->hasError());
    }

    public function testNoPass()
    {
        $model = $this->getFilledModel([
            'login' => 'test',
            'email' => 'test@test.te',
        ]);
        $this->assertFalse($model->validate('pass'));
        $this->assertTrue($model->hasErrors('pass'));
        $this->assertTrue($model->hasError());
    }

    public function testNoEmail()
    {
        $model = $this->getFilledModel([
            'login' => 'test',
            'pass' => 'dsadsad',
        ]);
        $this->assertFalse($model->validate('email'));
        $this->assertTrue($model->hasErrors('email'));
        $this->assertTrue($model->hasError());
    }

    public function testRightData()
    {
        Test::double(AuthAccessToken::className(), [
            'attributes' => ['access_token']
        ]);
        Test::double(AuthRefreshToken::className(), [
            'attributes' => ['refresh_token']
        ]);
        $proxyAccess = Test::double(AuthAccessToken::className(), [
            'createToken' => new AuthAccessToken([
                'access_token' => 'dsadsadsads'
            ])
        ]);
        $proxyRefresh = Test::double(AuthRefreshToken::className(), [
            'attributes' => ['refresh_token'],
            'createToken' => new AuthRefreshToken([
                'refresh_token' => 'dsadsadsads'
            ])
        ]);
        Test::double(User::className(), [
            'attributes' => ['id'],
            'toArray' => []
        ]);
        Test::double(User::className(), [
            'findUser' => new User([
                'id' => 1
            ])
        ]);
        $model = $this->getFilledModel([
            'pass' => '113',
            'login' => 'dsadsad',
            'email' => 'test@test.te'
        ]);
        $this->assertTrue($model->validate());
        $model->run();
        $this->assertNotNull($model->expires_id);
        $this->assertNotNull($model->profile);
        $this->assertNotNull($model->access_token);
        $this->assertNotNull($model->refresh_token);
        $proxyAccess->verifyInvoked('createToken', [
            1, AuthAccessToken::LIFE_TIME
            ]);
        $proxyRefresh->verifyInvoked('createToken', [
            1, 3600 * 24 * 365 * 10
        ]);
        $this->assertFalse($model->hasError());
    }




}