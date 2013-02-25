<?php

namespace Scrutinizer\Tests\ErrorReporter;

use Scrutinizer\ErrorReporter\ErrorSuppressingReporter;

class ErrorSuppressingReporterTest extends \PHPUnit_Framework_TestCase
{
    private $reporter;
    private $delegate;
    private $logger;

    public function testCallsLogger()
    {
        $ex = new \Exception('foo');
        $nestedEx = new \Exception('bar');

        $this->delegate->expects($this->once())
            ->method('reportException')
            ->with($ex)
            ->will($this->throwException($nestedEx));

        $this->logger->expects($this->once())
            ->method('error');

        $this->reporter->reportException($ex);
    }

    public function testEverythingGoesFine()
    {
        $ex = new \Exception('foo');

        $this->delegate->expects($this->once())
            ->method('reportException')
            ->with($ex);

        $this->logger->expects($this->never())
            ->method('error');

        $this->reporter->reportException($ex);
    }

    public function testExceptionInLoggerIsIgnored()
    {
        $ex = new \Exception('foo');
        $nestedEx = new \Exception('bar');

        $this->delegate->expects($this->once())
            ->method('reportException')
            ->with($ex)
            ->will($this->throwException($nestedEx));

        $this->logger->expects($this->once())
            ->method('error')
            ->will($this->throwException(new \Exception('logger')));

        $this->reporter->reportException($ex);
    }

    protected function setUp()
    {
        $this->reporter = new ErrorSuppressingReporter(
            $this->delegate = $this->getMock('Scrutinizer\ErrorReporter\ReporterInterface'),
            $this->logger = $this->getMock('Psr\Log\LoggerInterface')
        );
    }
}