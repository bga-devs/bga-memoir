<?php
namespace M44\Terrains;
use M44\Board;

class Road extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], [
      'road',
      'roadcurve',
      'roadFL',
      'roadFR',
      'roadX',
      'roadY',
      'droad',
      'droadX',
      'droadcurve',
      'droadFL',
      'droadFR',
      'wroad',
      'wroadcurve',
      'wroadFL',
      'wroadFR',
      'wroadX',
      'wroadY',
    ]);
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

      'droad' => [0, 6],
      'droadcurve' => [2, 10],
      'droadFL' => [0, 2, 6],
      'droadFR' => [0, 6, 10],
      'droadX' => [0, 2, 6, 8],

      'wroad' => [0, 6],
      'wroadcurve' => [2, 10],
      'wroadFL' => [0, 2, 6],
      'wroadFR' => [0, 6, 10],
      'wroadX' => [0, 2, 6, 8],
      'wroadY' => [0, 4, 8],
    ];
    $this->linkedDirections = [
      ALL_UNITS => $links[$row['tile'] ?? null] ?? [],
    ];
    parent::__construct($row);
  }
}
