<?php
namespace M44\Terrains;
use M44\Board;

class Road extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['road', 'roadcurve', 'roadFL', 'roadFR', 'roadX', 'roadY']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Roads');
    $this->number = 42;
    $this->desc = [
      \clienttranslate('Unit that starts its move on a Road and stays on it may move 1 additional hex'),
      clienttranslate('No combat restriction'),
    ];

    $this->isRoad = true;

    $links = [
      'road' => [0, 6],
      'roadcurve' => [2, 10],
      'roadFL' => [0, 2, 6],
      'roadFR' => [0, 6, 10],
      'roadX' => [0, 2, 6, 8],
      'roadY' => [0, 4, 8],
    ];
    $this->linkedDirections = $links[$row['tile'] ?? null] ?? [];
    parent::__construct($row);
  }
}
