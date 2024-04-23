<?php

// src/Service/ProfilerLogHandler.php
namespace App\Service;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\DataCollector\LoggerDataCollector;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ProfilerLogHandler extends AbstractProcessingHandler
{
    private $dataCollector;
    private $requestStack;

    public function __construct(DataCollectorInterface $dataCollector, RequestStack $requestStack, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->dataCollector = $dataCollector;
        $this->requestStack = $requestStack;
    }

    protected function write(LogRecord $record): void
    {
        $formattedRecord = $this->getFormatter()->format($record);
        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            $response = $this->getResponse();
            if ($response instanceof Response) {
                $this->dataCollector->collect($request, $response);
            }
        }
    }

    private function getResponse(): ?Response
    {
        // Implement your logic to retrieve the Response object
        // Return null if the Response object is not available
        return null;
    }
}
