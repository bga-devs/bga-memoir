<?php
namespace M44\Terrains;
use M44\Board;
use M44\Dice;
use M44\Core\Game;
use M44\Core\Notifications;
use M44\Managers\Medals;
use M44\Managers\Terrains;
use M44\Helpers\Log;

class SmokeScreen extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return false;
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Smoke Screen');
    $this->number = 68;
    $this->desc = [];
    $this->isBlockingLineOfSight= true;

    parent::__construct($row);
  }

}