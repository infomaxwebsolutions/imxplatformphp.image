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
 * Stores dimension information (width/height) and provides methods to check for landscape/portrait/square and getting ratios
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class Dimensions {

  /**
   * The width
   *
   * @var int
   */
  private $width = 0;

  /**
   * The height
   *
   * @var int
   */
  private $height = 0;

  /**
   *
   * @param int $width
   * @param int $height
   */
  public function __construct($width = 0, $height = 0) {
    $this->setWidth($width);
    $this->setHeight($height);
  }

  /**
   *
   * @return int
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   *
   * @param int $width
   * @return self
   */
  public function setWidth($width) {
    $this->width = (int) $width;
    return $this;
  }

  public function getHeight() {
    return $this->height;
  }

  /**
   *
   * @param int $height
   * @return self
   */
  public function setHeight($height) {
    $this->height = (int) $height;
    return $this;
  }

  /**
   * Compares the width to the height and returns true when the width is bigger
   *
   * @return bool True if the width is bigger than the height
   */
  public function isLandscape() {
    return $this->getWidth() > $this->getHeight();
  }

  /**
   * Compares the height to the width and returns true when the height is bigger
   *
   * @return bool True if the height is bigger than the width
   */
  public function isPortrait() {
    return $this->getHeight() > $this->getWidth();
  }

  /**
   * Compares the height to the width and returns true when both are identical
   *
   * @return bool True if height and width are identical
   */
  public function isSquare() {
    return $this->getHeight() === $this->getWidth();
  }

  /**
   * Gets the ratio from width to height by ... dividing width through height?
   *
   * @return float
   */
  public function getWidthHeightRatio() {
    return (float) ($this->getWidth() / $this->getHeight());
  }

  /**
   * Getting the ratio from height to width by ... dividing height through width?
   *
   * @return float
   */
  public function getHeightWidthRatio() {
    return (float) ($this->getHeight() / $this->getWidth());
  }
}
