<?php

use Symfony\Component\Dotenv\Dotenv;
use Medoo\Medoo;

class UsersCest
{
    private Medoo $db;

    private string $token;

    public function __construct()
    {
        // LOAD TEST ENV
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env.test');

        // 測試前使用SQLITE 準備Users表
        $this->db = new Medoo(['type' => $_ENV['DB_DRIVER'], 'database' => __DIR__ . "/../../" . $_ENV['DB_NAME']]);
    }

    public function _before(FunctionalTester $I)
    {
        // SQLITE 只需要 "PRIMARY KEY" 不需要主動添加 "AUTO_INCREMENT"
        $this->db->create("Users", [
            "id" => ["INTEGER", "NOT NULL", "PRIMARY KEY", "AUTOINCREMENT"],
            "name" => ["TEXT", "NOT NULL"]
        ]);

        // LOGIN
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/login', ["name" => "Nick", "password" => "123456"]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        list($data) = $I->grabDataFromResponseByJsonPath('$.data');
        $this->token = $data["access_token"];
    }

    public function _after(FunctionalTester $I)
    {
        // 測試前使用SQLITE 準備Users表
        $this->db->drop("Users");
    }

    public function Users_CRUD(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/user', ["name" => "Nick"]);
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
        $I->seeResponseContains('{"code":401,"data":{"姓名":["姓名 無法為空"]},"message":"提交格式有誤!"}');
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

}
