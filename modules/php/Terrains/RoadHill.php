<?php
namespace M44\Terrains;
use M44\Board;

class RoadHill extends Hill
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['hillroad', 'hillcurve']);
  }

  public function __construct($row)
  {
    $this->isRoad = true;
    $links = [
      'hillroad' => [0, 6],
      'hillcurve' => [2, 10],
    ];
    $this->linkedDirections = $links[$row['tile'] ?? null] ?? [];
    parent::__construct($row);

    $this->name = clienttranslate('Roads over Hills');
    $this->number = '42b';
    $this->desc = [
      \clienttranslate('Unit that starts its move on a Road and stays on it may move 1 additional hex'),
      clienttranslate('No combat restriction'),
    ];
  }
}
