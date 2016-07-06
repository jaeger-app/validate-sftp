<?php
/**
 * Jaeger
 *
 * @copyright	Copyright (c) 2015-2016, mithra62
 * @link		http://jaeger-app.com
 * @version		1.0
 * @filesource 	./tests/ConnectTest.php
 */
namespace JaegerApp\tests;

use JaegerApp\Validate;
use JaegerApp\Validate\Rules\Sftp\Writable;

/**
 * Jaeger - Valiate object Unit Tests
 *
 * Contains all the unit tests for the \mithra62\Valiate object
 *
 * @package Jaeger\Tests
 * @author Eric Lamb <eric@mithra62.com>
 */
class WritableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests the name of the rule
     */
    public function testName()
    {
        $dir = new Writable();
        $this->assertEquals($dir->getName(), 'sftp_writable');
    }

    /**
     * Tests that a directory can be determined true
     */
    public function testRuleFail()
    {
        $val = new Validate();
        $creds = $this->getSftpCreds();
        $creds['sftp_root'] = '/fdsafdsa';
        @$val->rule('sftp_writable', 'connection_field', $creds)->val(array(
            'connection_field' => 'Foo'
        ));
        $this->assertTrue($val->hasErrors());
    }

    /**
     * Tests that a directory can be determined true
     */
    public function testRuleSuccess()
    {
        $val = new Validate();
        @$val->rule('sftp_writable', 'connection_field', $this->getSftpCreds())
            ->val(array(
            'connection_field' => 'Foo'
        ));
        $this->assertFALSE($val->hasErrors());
    }

    /**
     * The SFTP Test Credentials
     *
     * @return array
     */
    protected function getSftpCreds()
    {
        return include 'data/sftpcreds.config.php';
    } 
}