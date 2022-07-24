<?php
use App\Utils\Validation;
class ValidationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    private Validation $validation;

    protected function _before()
    {
        $this->validation = new Validation();
    }

    // tests
    public function testSomeFeature()
    {
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
        ));
    }
}