<?php

namespace Scrutinizer\Tests\ErrorReporter;

use Scrutinizer\ErrorReporter\NullReporter;

class NullReporterTest extends \PHPUnit_Framework_TestCase
{
    private $reporter;

    public function testReportException()
    {
        $this->assertNull($this->reporter->reportException(new \Exception));
    }

    protected function setUp()
    {
        $this->reporter = new NullReporter();
    }
}