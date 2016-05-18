<?php
/*
 * Copyright 2016 Bastian Schwarz <bastian@codename-php.de>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @namespace
 */
namespace de\imxnet\imxplatformphp\image;

/**
 *
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class BinaryStringTest extends \de\codenamephp\platform\test\TestCase {

  /**
   *
   * @var type
   */
  private $sut = null;

  protected function setUp() {
    parent::setUp();

    $this->sut = new \de\imxnet\imxplatformphp\image\BinaryString(base64_decode($this->getBlankImage()));
  }

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage The given binary string was empty!
   */
  public function testfromString_canThrowInvalidArgumentException_whenBinaryStringIsEmpty() {
    $this->sut->fromString('');
  }

  /**
   * @expectedException \de\imxnet\imxplatformphp\image\InvalidImageException
   * @expectedExceptionMessage Image could not be created from string!
   */
  public function testfromString_canThrowInvalidImageException_whenGivenStringIsAValidImage() {
    $this->sut->fromString('some string');
  }

  public function testfromString_canSetMimeType() {
    self::assertEquals('image/png', $this->sut->fromString(base64_decode($this->getBlankImage()))->getMimeType());
  }

  private function getBlankImage() {
    return 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
        . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
        . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
        . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
  }
}
