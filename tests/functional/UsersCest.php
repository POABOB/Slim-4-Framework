<?php

use Helper\Functional;

class UsersCest
{
    private Functional $helper;

    private string $token;

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

    public function Users_CRUD(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/user', ["name" => "Nick", "password" => "123456"]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":200,"data":null,"message":"Success"}');

        $I->sendGET('/api/user');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'code' => 'integer',
            'message' => 'null',
            'data' => [
                [
                    "id" => 'integer|string',  
                    "name" => "string",
                ]
            ],
        ]);

        $I->sendPATCH('/api/user', ["id" => 1, "name" => "Bob"]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":200,"data":null,"message":"Success"}');

        // LOGIN
        $I->sendPOST('/api/login', ["name" => "Bob", "password" => "123456"]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        list($data) = $I->grabDataFromResponseByJsonPath('$.data');
        $this->token = $data["access_token"];

        $I->sendGET('/api/user');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'code' => 'integer',
            'message' => 'null',
            'data' => [
                [
                    "id" => 'integer|string',  
                    "name" => "string",
                ]
            ],
        ]);

        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDelete('/api/user', ["id" => 1]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":200,"data":null,"message":"Success"}');

        $I->sendGET('/api/user');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":200,"data":null,"message":null}');
    }

    public function InsertUserWithoutBody(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/user', []);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":401,"data":{"姓名":["姓名 無法為空"],"密碼":["密碼 無法為空"]},"message":"提交格式有誤!"}');
    }

    public function UpdateUserWithioutBody(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPATCH('/api/user');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":401,"data":{"ID":["ID 無法為空"],"姓名":["姓名 無法為空"]},"message":"提交格式有誤!"}');
    }

    public function DeleteUserWithioutBody(FunctionalTester $I): void
    {
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/api/user');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":401,"data":{"ID":["ID 無法為空"]},"message":"提交格式有誤!"}');
    }

    public function DeleteUserWithioutToken(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDelete('/api/user', ["id" => 1]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"code":403,"data":null,"message":"請先登入"}');
    }

}
