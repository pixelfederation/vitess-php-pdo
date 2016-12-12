<?php
/**
 * @author  mfris
 */

namespace VitessPdoTest\PDO;


use VitessPdo\PDO\ParamProcessor;
use PDO as CorePDO;

/**
 * Class ParamProcessorTest
 * @author mfris
 * @package VitessPdo\PDO
 */
class ParamProcessorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ParamProcessor
     */
    private $processor;

    /**
     *
     */
    protected function setUp()
    {
        $this->processor = new ParamProcessor();
    }

    /**
     *
     */
    public function testProcessBool()
    {
        $val = 1;
        $processed = $this->processor->process($val, CorePDO::PARAM_BOOL);
        $this->assertEquals(true, $processed);
    }

    /**
     *
     */
    public function testProcessInt()
    {
        $val = "1";
        $processed = $this->processor->process($val, CorePDO::PARAM_INT);
        $this->assertEquals(1, $processed);
    }

    /**
     *
     */
    public function testProcessNull()
    {
        $val = "1";
        $processed = $this->processor->process($val, CorePDO::PARAM_NULL);
        $this->assertEquals(null, $processed);
    }

    /**
     *
     */
    public function testProcessString()
    {
        $val = 10;
        $processed = $this->processor->process($val, CorePDO::PARAM_STR);
        $this->assertEquals('10', $processed);
    }

    /**
     *
     */
    public function testProcessLob()
    {
        $val = 10;
        $processed = $this->processor->process($val, CorePDO::PARAM_LOB);
        $this->assertEquals('10', $processed);
    }

    /**
     *
     */
    public function testProcessEscapedBool()
    {
        $val = 1;
        $processed = $this->processor->processEscaped($val, CorePDO::PARAM_BOOL);
        $this->assertEquals(true, $processed);
    }

    /**
     *
     */
    public function testProcessEscapedInt()
    {
        $val = "1";
        $processed = $this->processor->processEscaped($val, CorePDO::PARAM_INT);
        $this->assertEquals(1, $processed);
    }

    /**
     *
     */
    public function testProcessEscapedNull()
    {
        $val = "1";
        $processed = $this->processor->processEscaped($val, CorePDO::PARAM_NULL);
        $this->assertEquals(null, $processed);
    }

    /**
     *
     */
    public function testProcessEscapedString()
    {
        $val = "asd\\\"\0\n\r'\x1a";
        $processed = $this->processor->processEscaped($val, CorePDO::PARAM_STR);
        $this->assertEquals('asd\\\\"\\0\\n\\r\'\'\\Z', $processed);
    }

    /**
     *
     */
    public function testProcessEscapedLob()
    {
        $val = "asd\\\"\0\n\r'\x1a";
        $processed = $this->processor->processEscaped($val, CorePDO::PARAM_LOB);
        $this->assertEquals('asd\\\\"\\0\\n\\r\'\'\\Z', $processed);
    }
}
