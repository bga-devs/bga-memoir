<?php
namespace M44\Cards\Breakthrough;

class Attack extends \M44\Cards\Standard\Attack
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitles = [];
    $this->subtitle = clienttranslate('Order 3 Units + 1 On the Move');
    $t = clienttranslate('+ 1 On the Move');
    $this->texts = [
      [clienttranslate('Order 3 Units on the Left Flank'), $t],
      [clienttranslate('Order 3 Units in the Center'), $t],
      [clienttranslate('Order 3 Units on the Right Flank'), $t],
    ];
  }
}
