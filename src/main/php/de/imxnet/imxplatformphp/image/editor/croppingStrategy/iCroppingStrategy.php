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
namespace de\imxnet\imxplatformphp\image\editor\croppingStrategy;

use \de\imxnet\imxplatformphp\image\Box;
use \de\imxnet\imxplatformphp\image\Dimensions;
use \de\imxnet\imxplatformphp\image\Point;

/**
 * Interface for cropping strategy that calculates the box that will be cropped from a source image.
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
interface iCroppingStrategy {

  /**
   * Calculates the box that can be used to crop an image while zooming/maintaining a maximum of the orignal image while achieving the desired dimensions. Also, a Box can be passed
   * either to preset the cropping position or to pass the previous results of a cropping strategy in order to chain multiple strategies together
   *
   * @param Dimensions $desiredDimensions The desired width and height of the box
   * @param Dimensions $orignalDimensions The original dimension which will constrain the box, e.g. the dimensions of the source image
   * @param Point $focalPoint The focal point around which the box should be aligned (doesn't always have to be the center, but most likely is the desired center)
   * @param Box $initialBox An optional preset box, e.g. the result of a previous strategy
   * @return Box
   */
  public function getBox(Dimensions $desiredDimensions, Dimensions $orignalDimensions, Point $focalPoint, Box $initialBox = null);
}
