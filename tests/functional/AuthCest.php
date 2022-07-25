<?php

use Symfony\Component\Dotenv\Dotenv;
use Medoo\Medoo;

class AuthCest
{
    private Medoo $db;


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
    }

    public function _after(FunctionalTester $I)
    {
        // 測試前使用SQLITE 準備Users表
        $this->db->drop("Users");
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

