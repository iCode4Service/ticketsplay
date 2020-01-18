<?php
require('../src/TicketsplayValidator.php');

use PHPUnit\Framework\TestCase;

class TicketsplayValidatorTest extends TestCase
{
    private $validator;
    private $fields;

    protected function setUp()
    {
        $this->fields = [];
    }

    /**
     * @expectedExceptionMessage
     */
    public function testInvalidCommand()
    {
        $this->validator = new TicketsplaysValidator('fake_command', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: Invalid command name!', $is_valid);
    }

    public function testMissingRequiredField()
    {
        $this->fields = [
            'full' => 1
        ];
        $this->validator = new TicketsplaysValidator('get_tx_info_multi', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: The field "full" was passed but is not a valid field for the "get_tx_info_multi" command!', $is_valid);
    }

    public function testPassingInvalidField()
    {
        $this->fields = [
            'fake_field' => 1
        ];
        $this->validator = new TicketsplaysValidator('rates', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: The field "fake_field" was passed but is not a valid field for the "rates" command!', $is_valid);
    }

    public function testInvalidFieldStringType()
    {
        $fake_array = ['test1', 'test2'];
        $this->fields = [
            'txid' => $fake_array,
            'full' => 1
        ];
        $this->validator = new TicketsplaysValidator('get_tx_info', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: Field "txid" passed with value of "[ArrayData]" and data type of "array", but expected type is "string".', $is_valid);
    }

    public function testInvalidFieldIntegerType()
    {
        $this->fields = [
            'txid' => 'test1',
            'full' => 'test2'
        ];
        $this->validator = new TicketsplaysValidator('get_tx_info', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: Field "full" passed with value of "test2" and data type of "string", but expected type is "integer".', $is_valid);
    }

    public function testInvalidFieldArrayType()
    {
        $this->fields = [
            'wd' => 'test1'
        ];
        $this->validator = new TicketsplaysValidator('create_mass_withdrawal', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: Field "wd" passed with value of "test1" and data type of "string", but expected type is "array".', $is_valid);
    }

    public function testInvalidFieldUrlType()
    {
        $this->fields = [
            'currency' => 'TKTS',
            'ipn_url' => 'test.com'
        ];
        $this->validator = new TicketsplaysValidator('get_callback_address', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: Field "ipn_url" passed with value of "test.com" and data type of "string", but expected type is "url".', $is_valid);
    }

    public function testInvalidFieldEmailType()
    {
        $this->fields = [
            'amount' => 1,
            'currency1' => 'TKTS',
            'buyer_email' => 'notanemail.com'
        ];
        $this->validator = new TicketsplaysValidator('create_transaction', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: Field "buyer_email" passed with value of "notanemail.com" and data type of "string", but expected type is "email".', $is_valid);
    }

    public function testInvalidPermittedFieldValue()
    {
        $this->fields = [
            'all' => 3
        ];
        $this->validator = new TicketsplaysValidator('balances', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: Permitted values for the field "all" are [ 0 | 1 ] but the value passed was: 3', $is_valid);
    }

    public function testValidPermittedFieldValue()
    {
        $this->fields = [
            'all' => 1
        ];
        $this->validator = new TicketsplaysValidator('balances', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertTrue($is_valid);
    }

    public function testMissingOneOfField()
    {
        $this->fields = [
            'amount' => 1,
            'currency' => 'TKTS'
        ];
        $this->validator = new TicketsplaysValidator('create_transfer', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: At least one of the following fields must be passed: [ merchant | pbntag ]', $is_valid);
    }

    public function testTooManyOneOfFields()
    {
        $this->fields = [
            'amount' => 1,
            'currency' => 'TKTS',
            'merchant' => 'test1',
            'pbntag' => 'test2'
        ];
        $this->validator = new TicketsplaysValidator('create_transfer', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertEquals('Error: No more than one of the following fields can be passed: [ merchant | pbntag ]', $is_valid);
    }

    public function testNoFields()
    {
        $this->validator = new TicketsplaysValidator('get_basic_info', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertTrue($is_valid);
    }

    public function testAllFieldGroups()
    {
        $this->fields = [
            'amount' => 1,
            'currency' => 'TKTS',
            'merchant' => 'test1',
            'auto_confirm' => 1
        ];
        $this->validator = new TicketsplaysValidator('create_transfer', $this->fields);
        $is_valid = $this->validator->validate();
        $this->assertTrue($is_valid);
    }

    protected function tearDown()
    {
        $this->validator = null;
        $this->fields = null;
    }
}
