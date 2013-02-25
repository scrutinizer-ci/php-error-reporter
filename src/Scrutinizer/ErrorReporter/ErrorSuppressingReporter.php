<?php

namespace Scrutinizer\ErrorReporter;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Suppresses all errors which happen in the reporter.
 */
class ErrorSuppressingReporter implements ReporterInterface
{
    private $delegate;
    private $logger;

    public function __construct(ReporterInterface $reporter, LoggerInterface $logger = null)
    {
        $this->delegate = $reporter;
        $this->logger = $logger ?: new NullLogger();
    }

    public function reportException(\Exception $ex)
    {
        try {
            $this->delegate->reportException($ex);
        } catch (\Exception $nestedEx) {
            try {
                $this->logger->error('Exception "{nestedMessage}" occurred while reporting the exception "{message}".', array(
                    'nestedMessage' => $nestedEx->getMessage(),
                    'message' => $ex->getMessage(),
                    'exception' => $nestedEx,
                ));
            } catch (\Exception $loggerEx) {
                // Nothing we can do here, just ignore.
            }
        }
    }
}