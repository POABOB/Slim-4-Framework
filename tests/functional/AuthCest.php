<?php

use Helper\Functional;

class AuthCest
{
    private Functional $helper;

    public function __construct()
    {
        $this->helper = new Functional();
    }

    public function _before(FunctionalTester $I)
    {
        $this->helper->setUp();
    }

    public function _after(FunctionalTester $I)
    {
        $this->helper->tearDown();
    }

    public function Login(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/user', ["name" => "Bob", "password" => "123456"]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":200,"data":null,"message":"Success"}');

        $I->sendPOST('/api/login', ["name" => "Bob", "password" => "123456"]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'code' => 'integer',
            'message' => 'null',
            'data' => [
                "access_token" => 'string',  
                "token_type" => "string",
                "expires_in" => 'integer'
            ]
        ]);
    }

    public function Logout(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/api/logout');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":200,"data":null,"message":"Success"}');
    }

    public function LoginWithoutBody(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/login', []);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":401,"data":{"帳號":["帳號 無法為空"],"密碼":["密碼 無法為空"]},"message":"提交格式有誤!"}');
    }
}

