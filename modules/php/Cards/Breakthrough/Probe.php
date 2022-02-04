<?php
namespace M44\Cards\Breakthrough;

class Probe extends \M44\Cards\Standard\Probe
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->nUnitsOnTheMove = 2;
    $this->subtitles = [];
    $this->subtitle = clienttranslate('Order 2 Units + 2 On the Move');
    $t = clienttranslate('+ 2 On the Move');
    $this->texts = [
      [clienttranslate('Order 2 Units on the Left Flank'), $t],
      [clienttranslate('Order 2 Units in the Center'), $t],
      [clienttranslate('Order 2 Units on the Right Flank'), $t],
    ];
    $this->orderUnitsTitles = [
      clienttranslate('on the Left Flank + 2 on the Move'),
      clienttranslate('in the Center + 2 on the Move'),
      clienttranslate('on the Right Flank + 2 on the Move'),
    ];
  }
}
