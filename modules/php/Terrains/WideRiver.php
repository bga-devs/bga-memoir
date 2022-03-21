<?php
namespace M44\Terrains;
use M44\Board;

class WideRiver extends River
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['river', 'riverFL', 'riverFR', 'riverY', 'curve', 'pond', 'pmouth']) &&
      isset($hex['behavior']) &&
      $hex['behavior'] == 'WIDE_RIVER';
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Wide River');
    $this->number = 8;
  }

  public function isBlockingLineOfSight($unit, $target, $path)
  {
    if ($unit->getType() == \INFANTRY) {
      return true;
    }
    return false;
  }
}
