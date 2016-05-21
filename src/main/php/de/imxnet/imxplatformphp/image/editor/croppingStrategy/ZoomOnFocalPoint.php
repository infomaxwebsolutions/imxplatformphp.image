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
 * Cropping strategy that tries to keep a maximum of the original image by aligning the box step by step to the 3 closest borders, based on the original box around the focal point.
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class ZoomOnFocalPoint implements \de\imxnet\imxplatformphp\image\editor\croppingStrategy\iCroppingStrategy {

  const BORDER_TOP = 'top';
  const BORDER_RIGHT = 'right';
  const BORDER_BOTTOM = 'bottom';
  const BORDER_LEFT = 'left';

  /**
   * Gets the box for the optimal zoom while cutting as less as possible of the source image. This is done by aligning the 3 closest borders of the box to the borders of the
   * original box. This is finished as soon as only 1 unaligned border remains or the borders did not change in the last alignment cycle (which means the box cannot be aligned
   * anymore without breaking the desired ratio or getting out of bounds)
   *
   */
  public function getBox(Dimensions $desiredDimensions, Dimensions $orignalDimensions, Point $focalPoint, Box $initialBox = null) {
    $box = $this->setupBox($desiredDimensions, $initialBox);

    $borders = $this->calculateBorders($box, $orignalDimensions);
    $previousBorders = [];
    while(count($borders) > 1 && $borders != $previousBorders) {
      $this->alignBorder($box, $borders, $orignalDimensions);

      $previousBorders = $borders;
      $borders = $this->calculateBorders($box, $orignalDimensions);
    }
    $this->recenterOnFocalPoint($box, $focalPoint, $this->calculateBorders($box, $orignalDimensions, false));

    return $box;
  }

  /**
   * If a initial box is given, it is returned. if not, a new Box is created and a clone of the disered dimensions is used as its dimension, basicly starting with the "ideal" box
   * aligned at 0/0 (if the desired dimensions are the same as the orignal dimensions and the focal point is in the center, you would be done already)
   *
   * @param Dimensions $desiredDimensions
   * @param Box $initialBox
   * @return Box
   */
  public function setupBox(Dimensions $desiredDimensions, Box $initialBox = null) {
    if($initialBox instanceof Box) {
      $box = $initialBox;
    }else {
      $box = (new Box())->setDimensions(clone $desiredDimensions);
    }
    return $box;
  }

  /**
   * Picks the border of the box that is closest to a border of the source image and aligns the border, then increasing the width/height for that distance while maintaining the
   * desired width/height ratio. Because the closest border is picked, the dimensions are increased by the smallest amount possible, increasing the chance that the new box doesn't
   * have to be corrected.
   *
   * E.g. if the closest border is the top one, the top borders will be aligned by setting the height of the box to the current height + the distance of the closest border. The width
   * will be set keeping the desired ratio. Then, the y will be set to 0 (so the box is aligned with the top) and the x is moved left by the same amount as the border distance. If
   * this would move the x out of bounds, it will be set to 0. After that, the entire box is checked if it still fits within the image. if not, the width and height will be reduced
   * to fit while keeping the aspect ratio.
   *
   * Basicly, the box is enlarged while trying to keep the focal point in the center and mainting the aspect ratio while keeping the box within the source image.
   *
   */
  public function alignBorder(Box $box, array $borderDistances, Dimensions $originalDimensions) {
    $closestBorder = array_search(min($borderDistances), $borderDistances);
    $desiredRatio = $box->getDimensions()->getWidthHeightRatio();

    if(in_array($closestBorder, [static::BORDER_TOP, static::BORDER_BOTTOM], true)) {
      $box->getDimensions()
          ->setHeight(min($box->getDimensions()->getHeight() + $borderDistances[$closestBorder], $originalDimensions->getHeight()))
          ->setWidth($box->getDimensions()->getHeight() * $desiredRatio);

      if($closestBorder === static::BORDER_TOP) {
        $box->getTopLeftCorner()->setY(0)->setX(max($box->getTopLeftCorner()->getX() - $borderDistances[$closestBorder], 0));
      }else {
        $box->getTopLeftCorner()->setY(max($originalDimensions->getHeight() - $box->getDimensions()->getHeight(), 0))
            ->setX(max($box->getTopLeftCorner()->getX() - $borderDistances[$closestBorder], 0));
      }

      if($box->getTopRightCorner()->getX() > $originalDimensions->getWidth()) {
        $box->getDimensions()->setWidth($originalDimensions->getWidth() - $box->getTopLeftCorner()->getX())->setHeight($box->getDimensions()->getWidth() / $desiredRatio);
      }
    }elseif(in_array($closestBorder, [static::BORDER_RIGHT, static::BORDER_LEFT], true)) {
      $box->getDimensions()
          ->setWidth(min($box->getDimensions()->getWidth() + $borderDistances[$closestBorder], $originalDimensions->getWidth()))
          ->setHeight($box->getDimensions()->getWidth() / $desiredRatio);

      if($closestBorder === static::BORDER_RIGHT) {
        $box->getTopLeftCorner()->setY(max($box->getTopLeftCorner()->getY() - $borderDistances[$closestBorder], 0))
            ->setX(max($originalDimensions->getWidth() - $box->getDimensions()->getWidth(), 0));
      }else {
        $box->getTopLeftCorner()->setX(0)->setY(max($box->getTopLeftCorner()->getY() - $borderDistances[$closestBorder], 0));
      }

      if($box->getBottomLeftCorner()->getY() > $originalDimensions->getHeight()) {
        $box->getDimensions()->setHeight($originalDimensions->getHeight() - $box->getTopLeftCorner()->getY())->setWidth($box->getDimensions()->getHeight() * $desiredRatio);
      }
    }
    return $this;
  }

  /**
   * Tries to move the box to be centered on the focal point by getting the offset of the current top left x/y to the ideal top left x/y that would center the box and then moving
   * it until it hits the ideal point or the border of the original box (whatever happens first)
   *
   * @param Box $box
   * @param Point $focalPoint
   * @param array $borderDistances
   * @return self
   */
  public function recenterOnFocalPoint(Box $box, Point $focalPoint, array $borderDistances) {
    $idealTopLeftX = $focalPoint->getX() - $box->getDimensions()->getWidth() / 2;
    $idealTopLeftY = $focalPoint->getY() - $box->getDimensions()->getHeight() / 2;

    $neededOffsetX = $idealTopLeftX - $box->getTopLeftCorner()->getX();
    $neededOffsetY = $idealTopLeftY - $box->getTopLeftCorner()->getY();

    if($neededOffsetX > 0) {
      $box->getTopLeftCorner()->setX($box->getTopLeftCorner()->getX() + min($neededOffsetX, $borderDistances[static::BORDER_RIGHT]));
    }elseif($neededOffsetX < 0) {
      $box->getTopLeftCorner()->setX($box->getTopLeftCorner()->getX() - min($neededOffsetX * -1, $borderDistances[static::BORDER_LEFT]));
    }

    if($neededOffsetY > 0) {
      $box->getTopLeftCorner()->setY($box->getTopLeftCorner()->getY() + min($neededOffsetY, $borderDistances[static::BORDER_BOTTOM]));
    }elseif($neededOffsetY < 0) {
      $box->getTopLeftCorner()->setY($box->getTopLeftCorner()->getY() - min($neededOffsetY * -1, $borderDistances[static::BORDER_TOP]));
    }
    return $this;
  }

  /**
   * Calculates the distances from the borders of the box to the borders of the original image. Negative values are unsigned since the distance is always positive (with a negative
   * distance, a box that would overlap the original image would be pushed even further in the wrong direction since adding a negative value is substracting).
   *
   *
   */
  public function calculateBorders(Box $box, Dimensions $originalDimensions, $filterZeroBorders = true) {
    $borderDistances = [
      static::BORDER_TOP => $box->getTopLeftCorner()->getY(),
      static::BORDER_RIGHT => $originalDimensions->getWidth() - $box->getTopRightCorner()->getX(),
      static::BORDER_BOTTOM => $originalDimensions->getHeight() - $box->getBottomLeftCorner()->getY(),
      static::BORDER_LEFT => $box->getTopLeftCorner()->getX(),
    ];
    if($filterZeroBorders) {
      $borderDistances = array_filter($borderDistances);
    }

    return array_map(function($border) {
      if($border < 0) {
        $border *= -1;
      }
      return $border;
    }, $borderDistances);
  }
}
