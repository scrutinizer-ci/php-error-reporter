<?php

namespace Scrutinizer\ErrorReporter;

class NullReporter implements ReporterInterface
{
    public function reportException(\Exception $ex)
    {
    }
}