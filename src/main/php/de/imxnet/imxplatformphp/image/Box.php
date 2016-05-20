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
 * Simple box that is defined by a top left x/y coordinate, a width and a height
 *
 * Can be used to store box coordinates, e.g. for cropping
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class Box {

  /**
   * The top left coordinate (x/y) of the box
   *
   * @var Point
   */
  private $topLeftCorner = null;

  /**
   * The dimensions (width/height) of the box
   *
   * @var Dimensions
   */
  private $dimensions = null;

  /**
   * Sets an empty Point for topLeftCorner and empty dimensions
   */
  public function __construct() {
    $this->setTopLeftCorner(new Point());
    $this->setDimensions(new Dimensions());
  }

  /**
   *
   * @return Point
   */
  public function getTopLeftCorner() {
    return $this->topLeftCorner;
  }

  /**
   *
   * @return Dimensions
   */
  public function getDimensions() {
    return $this->dimensions;
  }

  /**
   *
   * @param \de\imxnet\imxplatformphp\image\Dimensions $dimensions
   * @return self
   */
  public function setDimensions(Dimensions $dimensions) {
    $this->dimensions = $dimensions;
    return $this;
  }

  /**
   *
   * @param \de\imxnet\imxplatformphp\image\Point $topLeftCorner
   * @return self
   */
  public function setTopLeftCorner(Point $topLeftCorner) {
    $this->topLeftCorner = $topLeftCorner;
    return $this;
  }

  /**
   * Gets the top right corner by creating a new point with current x + width as x and the current y as y
   *
   * The reference is not kept so manipulating values doesn't affect the box
   *
   * @return \de\imxnet\imxplatformphp\image\Point
   */
  public function getTopRightCorner() {
    return new Point($this->getTopLeftCorner()->getX() + $this->getDimensions()->getWidth(), $this->getTopLeftCorner()->getY());
  }

  /**
   * Gets the bottom right corner by creating a new point with current x + the current width as x and the current y + the current height as y
   *
   * The reference is not kept so manipulating values doesn't affect the box
   *
   * @return \de\imxnet\imxplatformphp\image\Point
   */
  public function getBottomRightCorner() {
    return new Point($this->getTopLeftCorner()->getX() + $this->getDimensions()->getWidth(), $this->getTopLeftCorner()->getY() + $this->getDimensions()->getHeight());
  }

  /**
   * Gets the bottom left corner by creating a new point with current x as x and the current y + the current height as y
   *
   * The reference is not kept so manipulating values doesn't affect the box
   *
   * @return \de\imxnet\imxplatformphp\image\Point
   */
  public function getBottomLeftCorner() {
    return new Point($this->getTopLeftCorner()->getX(), $this->getTopLeftCorner()->getY() + $this->getDimensions()->getHeight());
  }
}
