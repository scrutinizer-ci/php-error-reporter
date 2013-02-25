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

namespace Scrutinizer\Tests\ErrorReporter\Converter;

use Scrutinizer\ErrorReporter\Converter\ExceptionConverter;

class ExceptionConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var ExceptionConverter */
    private $converter;

    public function testConvert()
    {
        $data = $this->converter->convert(new \Exception('Test', 123, new \Exception('abc')));

        $this->assertCount(2, $data);
        $this->assertTrue(isset($data[0]['message']), 'Message exists');
        $this->assertTrue(isset($data[0]['code']), 'Code exists');
        $this->assertTrue(isset($data[0]['trace']), 'Trace exists');
        $this->assertTrue(isset($data[1]['message']), 'Message exists');
        $this->assertTrue(isset($data[1]['code']), 'Code exists');
        $this->assertTrue(isset($data[1]['trace']), 'Trace exists');
    }

    protected function setUp()
    {
        $this->converter = new ExceptionConverter();
    }
}