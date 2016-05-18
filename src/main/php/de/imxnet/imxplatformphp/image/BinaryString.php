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
 * Image implementation that stores the image as binary string. Since the string is stored within php, the image is kept in memory elimenating additional file lookups, but also
 * increasing memory usage.
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class BinaryString implements iImage {

  /**
   * The image resource created from the binary string
   *
   * @var resource
   */
  private $image = null;

  /**
   * The mime type of the image
   *
   * @var string
   */
  private $mimeType = '';

  /**
   * Calls self::fromString with the given $binaryString
   *
   * @param string $binaryString
   * @throws \InvalidArgumentException if the binaryString is empty
   * @throws InvalidImageException when the image could not be crated from string
   * @throws InvalidMimeTypeException when the image mime type could be read from string
   */
  public function __construct($binaryString) {
    $this->fromString($binaryString);
  }

  /**
   *
   * @return resource
   */
  public function getImage() {
    return $this->image;
  }

  /**
   * Sets the instance resource to the given resource.
   *
   * ATTENTION: This only sets the member variable. Calculated info like the mime type are not affected by this. If you have a new binary string, create a new instance or use
   * self::fromString()
   *
   * @param resource $image
   * @return self
   */
  public function setImage($image) {
    $this->image = $image;
    return $this;
  }

  /**
   * Gets the width of the image in pixels using the instance resource
   *
   * @return int
   */
  public function getWidth() {
    return imagesx($this->getImage());
  }

  /**
   * Gets the height of the image in pixels using the instance resource
   *
   * @return int
   */
  public function getHeight() {
    return imagesy($this->getImage());
  }

  /**
   *
   * @return string
   */
  public function getMimeType() {
    return $this->mimeType;
  }

  /**
   *
   * @param string $mimeType
   * @return self
   */
  public function setMimeType($mimeType) {
    $this->mimeType = (string) $mimeType;
    return $this;
  }

  /**
   * Creates a resource from the given binary string. If this fails (imagecreatefromstring returns false or anything else than a resource), an InvalidImageException is thrown.
   *
   * If the image could be created, the mime-type will be read from the binary string using getimagesizefromstring. If the mime-type is not within the predefined image constants
   * (index 2 in result is bigger that IMAGETYPE_COUNT - 1) an InvalidMimeTypeException is thrown.
   * If the mime-type is valid, is is transformed to string mime-type using image_type_to_mime_type with index 2
   *
   * If the image is a png (mime-type constant is IMAGETYPE_PNG) the alphablending is applied to the resource so transparancy is correctly supported.
   *
   * Last, the mime-type and the resource is set to the instance.
   *
   * @param string $binaryString
   * @return \de\imxnet\imxplatformphp\image\BinaryString
   * @throws \InvalidArgumentException if the binaryString is empty
   * @throws InvalidImageException when the image could not be crated from string
   * @throws InvalidMimeTypeException when the image mime type could be read from string
   */
  public function fromString($binaryString) {
    if($binaryString === '') {
      throw new \InvalidArgumentException('The given binary string was empty!');
    }

    $image = @imagecreatefromstring($binaryString); //error supression is used since we want to throw exceptions and warnings are converted to errors in PHPUnit
    if($image === false || !is_resource($image)) {
      throw new InvalidImageException('Image could not be created from string!');
    }

    $imageData = getimagesizefromstring($binaryString);
    if($imageData[2] > IMAGETYPE_COUNT - 1) {
      throw new InvalidMimeTypeException('The mime-type could not be read from string!');
    }
    $mimeType = image_type_to_mime_type($imageData[2]);

    if($imageData[2] === IMAGETYPE_PNG) {
      imagealphablending($image, true);
      imagesavealpha($image, true);
    }

    $this->setMimeType($mimeType);
    $this->setImage($image);
    return $this;
  }
}
