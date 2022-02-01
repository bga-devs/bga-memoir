<?php
namespace M44\Cards\Breakthrough;

class Probe extends \M44\Cards\Standard\Probe
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitles = [];
    $this->subtitle = clienttranslate('Order 2 Units + 2 On the Move');
    $t = clienttranslate('+ 2 On the Move');
    $this->texts = [
      [clienttranslate('Order 2 Units on the Left Flank'), $t],
      [clienttranslate('Order 2 Units in the Center'), $t],
      [clienttranslate('Order 2 Units on the Right Flank'), $t],
    ];
  }
}
