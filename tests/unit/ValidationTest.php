<?php
use App\Utils\Validation;
class ValidationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    private Validation $validation;

    // tests
    public function testValidation()
    {
      // Valid
      $this->validation = new Validation();
      $this->assertEmpty($this->validation->validate(
          [
            "Required" => "A",
            "Min" => "B",
            "Max" => "C",
            "Number" => 1,
            "Float" => 1.1,
            "Alpha" => "F",
            "Space" => "G",
            "Email" => "a@gmail.com",
            "Same" => "I",
            "ID" => "A123456789"
          ],
          [
            "Required" => ["required"],
            "Min" => ["minLen" => 1],
            "Max" => ["maxLen" => 1],
            "Number" => ["numeric"],
            "Float" => ["float"],
            "Alpha" => ["alpha"],
            "Space" => ["space"],
            "Email" => ["email"],
            "Same" => ['same' => "I"],
            "ID" => ['id_number'],
          ]
        )
      );

      // InValid
      $this->validation = new Validation();
      $this->validation->validate(
        ["Min" => "B"],
        ["Min" => ["minLen" => 2]]
      );
      $this->assertEquals(
        ['Min' =>  ['Min 最小長度應為 2 個字元']],
        $this->validation->error()
      );

      $this->validation = new Validation();
      $this->validation->validate(
        ["Max" => "BBB"],
        ["Max" => ["maxLen" => 2]]
      );
      $this->assertEquals(
        ['Max' =>  ['Max 最大長度應為 2 個字元']],
        $this->validation->error()
      );

      $this->validation = new Validation();
      $this->validation->validate(
        ["Numeric" => "B"],
        ["Numeric" => ["numeric"]]
      );
      $this->assertEquals(
        ['Numeric' =>  ['Numeric 應為數字']],
        $this->validation->error()
      );

      $this->validation = new Validation();
      $this->validation->validate(
        ["Float" => "B"],
        ["Float" => ["float"]]
      );
      $this->assertEquals(
        ['Float' =>  ['Float 應為浮點數']],
        $this->validation->error()
      );

      $this->validation = new Validation();
      $this->validation->validate(
        ["Alpha" => "1"],
        ["Alpha" => ["alpha"]]
      );
      $this->assertEquals(
        ['Alpha' =>  ['Alpha 應為字母']],
        $this->validation->error()
      );

      $this->validation = new Validation();
      $this->validation->validate(
        ["Space" => "A A"],
        ["Space" => ["space"]]
      );
      $this->assertEquals(
        ['Space' =>  ['Space 不應有空格']],
        $this->validation->error()
      );

      $this->validation = new Validation();
      $this->validation->validate(
        ["Email" => "B"],
        ["Email" => ["email"]]
      );
      $this->assertEquals(
        ['Email' =>  ['Email 不為Email格式']],
        $this->validation->error()
      );

      $this->validation = new Validation();
      $this->validation->validate(
        ["Same" => "B"],
        ["Same" => ["same" => "A"]]
      );
      $this->assertEquals(
        ['Same' =>  ['Same 輸入要一致']],
        $this->validation->error()
      );

      $this->validation = new Validation();
      $this->validation->validate(
        ["id_number" => "B"],
        ["id_number" => ["id_number"]]
      );
      $this->assertEquals(
        ['id_number' =>  ['Id_number 不為身份證格式']],
        $this->validation->error()
      );
    }
}