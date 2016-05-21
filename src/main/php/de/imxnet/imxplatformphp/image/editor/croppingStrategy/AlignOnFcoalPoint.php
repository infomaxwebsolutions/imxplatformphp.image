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
 * Tries to align the Box centered around the focal point. If the box would get out of bounds of the original dimensions, the box will be moved to align with the borders and
 * shrinked to fit within the orignal dimensions (if nessecary).
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class AlignOnFcoalPoint implements \de\imxnet\imxplatformphp\image\editor\croppingStrategy\iCroppingStrategy {

  /**
   * Centers the box around the focal point. There is no zooming of the box, if at all, the box is shrunk if it does not fit into the original dimensions. The result will be a box,
   * centered on the focal point (if possible) that fits within the original dimesnsions while keeping the aspect ratio.
   *
   * If possible means: If the focus point is located so that the desired dimension would move the box out of bounds if the centering was kept, that moving into bounds and the
   * keeping of the aspect ratio are considered more important than the "centerness" of the focal point
   *
   * The following steps are performed:
   *
   *  - First, the box is setup from the initial box. if there is a initialBox given, it will be used. If not, an empty box is created and the dimensions are set to a clone of the
   *    desired dimensions
   *  - Then, the box is constrained to the dimensions of the original dimensions, meaning: If the width is bigger than the original width, it will be shrunk to the original width
   *    and the height is set in the same ratio. After, the same is done for the height, so the result will definatley fit within the orignal dimensions (if the box will be aligned
   *    at 0/0 or around the center of the original dimensions).
   *  - Then, the box is centered on the focus point by movin the top left corner. Offset is x/y of the focus point with width and height cut but half. If this would move the box
   *    out of bounds, it will be stopped when the coordinate reaches 0
   *  - Last, the top left corner is further moved if the box is still out of bounds, agian stopping at 0/0
   *
   * @param Dimensions $desiredDimensions
   * @param Dimensions $originalDimensions
   * @param Point $focalPoint
   * @param Box $initialBox
   * @return type
   */
  public function getBox(Dimensions $desiredDimensions, Dimensions $originalDimensions, Point $focalPoint, Box $initialBox = null) {
    $box = $this->setupBox($desiredDimensions, $initialBox);

    $this->constraintDimensions($box, $originalDimensions)
        ->centerOnFocalPoint($box, $focalPoint)
        ->moveIntoBounds($box, $originalDimensions);

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
   * Constrains the box to the originalDimensions.
   *
   * First, the width is compared to the original width. If it is bigger, the boxes width is shrunk to the originalWidth and the height is shrunk by the new width, divided by the
   * desired width/height ratio
   *
   * Then, the same is done for the height, only that the height is multiplied by the widthHeight ratio
   *
   * @param Box $box
   * @param Dimensions $originalDimensions
   * @return self
   */
  public function constraintDimensions(Box $box, Dimensions $originalDimensions) {
    $desiredRatio = $box->getDimensions()->getWidthHeightRatio();
    if($box->getDimensions()->getWidth() > $originalDimensions->getWidth()) {
      $box->getDimensions()->setWidth($originalDimensions->getWidth())->setHeight($box->getDimensions()->getWidth() / $desiredRatio);
    }

    if($box->getDimensions()->getHeight() > $originalDimensions->getHeight()) {
      $box->getDimensions()->setHeight($originalDimensions->getHeight())->setWidth($box->getDimensions()->getHeight() * $desiredRatio);
    }

    return $this;
  }

  /**
   * Centers the box on the focal point by offsetting the x/y coordinate of the top left corner to the half of either dimension. It stops if the top left corner would move out of
   * bounds
   *
   * Note: This only constraints the top left corner. The other side might still be out of bounds, but that's what the other methods are for
   * @param Box $box
   * @param Point $focalPoint
   * @return self
   */
  public function centerOnFocalPoint(Box $box, Point $focalPoint) {
    $box->getTopLeftCorner()->setX(max(round($focalPoint->getX() - $box->getDimensions()->getWidth() / 2), 0));
    $box->getTopLeftCorner()->setY(max(round($focalPoint->getY() - $box->getDimensions()->getHeight() / 2), 0));
    return $this;
  }

  /**
   * Moves the box back into bound (only if it is out of bounds) by checking if the top right corner is outside of the original width. If so, the x of the top left corner is moved
   * to the left, stopping at 0 (so we are not out of bounds agian).
   *
   * Note: This only constraints the top left corner. The other side might still be out of bounds, but that's what the other methods are for (self::constraintDimensions() maxes
   * the box width to the original width, so if the top left corner is 0/0, it cannot be out of bounds anymore). This might offset the focal point so it's not the center anymore.
   *
   * @param Box $box
   * @param Dimensions $originalDimensions
   * @return self
   */
  public function moveIntoBounds(Box $box, Dimensions $originalDimensions) {
    if($box->getTopRightCorner()->getX() > $originalDimensions->getWidth()) {
      $box->getTopLeftCorner()->setX(max($originalDimensions->getWidth() - $box->getDimensions()->getWidth(), 0));
    }
    if($box->getBottomLeftCorner()->getY() > $originalDimensions->getHeight()) {
      $box->getTopLeftCorner()->setY(max($originalDimensions->getHeight() - $box->getDimensions()->getHeight(), 0));
    }
    return $this;
  }
}
