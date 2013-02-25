<?php

namespace Scrutinizer\Tests\ErrorReporter;

use Scrutinizer\ErrorReporter\HttpReporter;

class HttpReporterTest extends \PHPUnit_Framework_TestCase
{
    private $reporter;
    private $client;
    private $converter;

    public function testSendsException()
    {
        $ex = new \Exception('foo');

        $request = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('send');

        $this->client->expects($this->once())
            ->method('post')
            ->with('uri/path', array('Content-Type' => 'application/json'), json_encode(array(
                'machine_name' => 'foo',
                'process_name' => 'bar',
                'exceptions' => array()
            )))
            ->will($this->returnValue($request));

        $this->converter->expects($this->once())
            ->method('convert')
            ->with($ex)
            ->will($this->returnValue(array()));

        $this->reporter->reportException($ex);
    }

    protected function setUp()
    {
        $this->reporter = new HttpReporter(
            $this->client = $this->getMock('Guzzle\Http\ClientInterface'),
            'uri/path',
            'foo',
            'bar',
            $this->converter = $this->getMock('Scrutinizer\ErrorReporter\Converter\ExceptionConverter')
        );
    }
}