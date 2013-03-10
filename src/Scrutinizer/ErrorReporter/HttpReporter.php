<?php

/*
 * Copyright 2013 Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Scrutinizer\ErrorReporter;

use Guzzle\Http\ClientInterface;
use Scrutinizer\ErrorReporter\Converter\ExceptionConverter;

/**
 * Reports an exception via HTTP.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class HttpReporter implements ReporterInterface
{
    private $client;
    private $uri;
    private $revision;
    private $machineName;
    private $processName;
    private $converter;

    public function __construct(ClientInterface $client, $uri, $revision = null, $machineName = null, $processName = null, ExceptionConverter $converter = null)
    {
        $this->client = $client;
        $this->uri = $uri;
        $this->revision = $revision;
        $this->machineName = $machineName;
        $this->processName = $processName;
        $this->converter = $converter ?: new ExceptionConverter();
    }

    public function reportException(\Exception $ex)
    {
        $data = array(
            'revision' => $this->revision,
            'machine_name' => $this->machineName,
            'process_name' => $this->processName,
            'exceptions' => $this->converter->convert($ex),
        );

        $this->client->post($this->uri, array('Content-Type' => 'application/json'), json_encode($data))->send();
    }
}