<?php
/**
 * @namespace
 */
namespace de\imxnet\imxplatformphp\image;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-05-20 at 23:04:40.
 */
class DimensionsTest extends \de\codenamephp\platform\test\TestCase {

  /**
   * @var Dimensions
   */
  private $sut = null;

  protected function setUp() {
    $this->sut = new Dimensions();
  }

  public function testgetWidthHeightRatio() {
    $this->sut->setHeight(300)->setWidth(150);

    self::assertSame(0.5, $this->sut->getWidthHeightRatio());
  }

  public function testgetHeightWidthRatio() {
    $this->sut->setHeight(300)->setWidth(150);

    self::assertSame(2.0, $this->sut->getHeightWidthRatio());
  }

  public function testisLandscape_canReturnTrue_whenWidthIsBiggerThanHeight() {
    $this->sut->setWidth(200)->setHeight(100);

    self::assertTrue($this->sut->isLandscape());
  }

  public function testisLandscape_canReturnFalse_whenHeightIsBiggerThanWidth() {
    $this->sut->setWidth(200)->setHeight(300);

    self::assertFalse($this->sut->isLandscape());
  }

  public function testisPortrait_canReturnTrue_whenHeightIsBiggerThanWidth() {
    $this->sut->setWidth(200)->setHeight(300);

    self::assertTrue($this->sut->isPortrait());
  }

  public function testisPortrait_canReturnFalse_whenWidthIsBiggerThanHeight() {
    $this->sut->setWidth(500)->setHeight(300);

    self::assertFalse($this->sut->isPortrait());
  }

  public function testisSquare_canReturnFalse_whenWidthIsBiggerThanHeight() {
    $this->sut->setWidth(500)->setHeight(300);

    self::assertFalse($this->sut->isSquare());
  }

  public function testisSquare_canReturnFalse_whenHeightIsBiggerThanWidth() {
    $this->sut->setWidth(500)->setHeight(600);

    self::assertFalse($this->sut->isSquare());
  }

  public function testisSquare_canReturnTrue_whenHeightIsEqualToWidth() {
    $this->sut->setWidth(500)->setHeight(500);

    self::assertTrue($this->sut->isSquare());
  }
}
