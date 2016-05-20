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
 * Simple point, consisting of a x/y coordinate pair
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class Point {

  /**
   * The x coordinate
   *
   * @var int
   */
  private $x = 0;

  /**
   * The y coordinate
   *
   * @var int
   */
  private $y = 0;

  public function __construct($x = 0, $y = 0) {
    $this->setX($x);
    $this->setY($y);
  }

  /**
   *
   * @return int
   */
  public function getX() {
    return $this->x;
  }

  /**
   *
   * @param int $x
   * @return self
   */
  public function setX($x) {
    $this->x = (int) $x;
    return $this;
  }

  /**
   *
   * @return int
   */
  public function getY() {
    return $this->y;
  }

  /**
   *
   * @param int $y
   * @return self
   */
  public function setY($y) {
    $this->y = (int) $y;
    return $this;
  }
}
