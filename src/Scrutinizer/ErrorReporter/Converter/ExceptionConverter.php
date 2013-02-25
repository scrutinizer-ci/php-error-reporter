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

namespace Scrutinizer\ErrorReporter\Converter;

class ExceptionConverter
{
    private $basePath;
    private $basePathLength;

    public function __construct($basePath = '')
    {
        $this->basePath = $basePath;
        $this->basePathLength = strlen($this->basePath);
    }

    /**
     * Converts an exception to an array.
     *
     * @param \Exception $ex
     * @return array
     */
    public function convert(\Exception $ex)
    {
        $exceptions = array();

        do {
            $exceptions[] = $this->convertException($ex);
        } while (null !== $ex = $ex->getPrevious());

        return $exceptions;
    }

    private function convertException(\Exception $ex)
    {
        $data = array(
            'message' => $ex->getMessage(),
            'code' => $ex->getCode(),
            'trace' => array(),
        );

        foreach ($ex->getTrace() as $trace) {
            $filePath = null;

            if (isset($trace['file'])) {
                $filePath = ! empty($this->basePath) && 0 === strpos($trace['file'], $this->basePath) ?
                    substr($trace['file'], $this->basePathLength) : $trace['file'];
            }

            $className = $methodName = $functionName = null;
            if (isset($trace['class'])) {
                $className = $trace['class'];

                if (isset($trace['function'])) {
                    $methodName = $trace['function'];
                }
            } elseif (isset($trace['function'])) {
                $functionName = $trace['function'];
            }

            $args = array();
            if (isset($trace['args'])) {
                foreach ($trace['args'] as $arg) {
                    $args[] = $this->convertArgument($arg);
                }
            }

            $data['trace'][] = array(
                'file' => $filePath,
                'line' => isset($trace['line']) ? (integer) $trace['line'] : null,
                'class_name' => $className,
                'method_name' => $methodName,
                'function_name' => $functionName,
                'arguments' => $args,
            );
        }

        return $data;
    }

    private function convertArgument($arg)
    {
        if (null === $arg) {
            return array('type' => 'null', 'value' => null);
        }

        if (is_bool($arg)) {
            return array('type' => 'boolean', 'value' => $arg ? 'true' : 'false');
        }

        if (is_integer($arg)) {
            return array('type' => 'integer', 'value' => $arg);
        }

        if (is_string($arg)) {
            return array('type' => 'string', 'value' => $arg);
        }

        if (is_resource($arg)) {
            return array('type' => 'resource', 'value' => get_resource_type($arg));
        }

        if (is_array($arg)) {
            return array('type' => 'array', 'value' => json_encode($this->sanitizeArray($arg)));
        }

        if (is_object($arg)) {
            $str = get_class($arg);

            if (method_exists($arg, '__toString')) {
                $str .= ': '.$arg;
            }

            return array('type' => 'object', 'value' => $str);
        }

        return array('type' => 'unknown', 'value' => gettype($arg));
    }

    private function sanitizeArray(array $arr)
    {
        $newArr = array();

        foreach ($arr as $k => $v) {
            if (null === $v || is_scalar($v)) {
                $newArr[$k] = $v;
            } elseif (is_array($v)) {
                $newArr[$k] = $this->sanitizeArray($v);
            } elseif (is_resource($v)) {
                $newArr[$k] = 'resource: '.get_resource_type($v);
            } elseif (is_object($v)) {
                $str = get_class($v);
                if (method_exists($v, '__toString')) {
                    $str .= ': '.$v;
                }

                $newArr[$k] = $str;
            } else {
                $newArr[$k] = 'unknown value';
            }
        }

        return $newArr;
    }
}