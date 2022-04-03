<?php

namespace Scrutinizer\ErrorReporter;

class NullReporter implements ReporterInterface
{
    public function reportException(\Throwable $ex)
    {
    }
}
