<?php
namespace M44\Cards\Breakthrough;

class Recon extends \M44\Cards\Standard\Recon
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitles = [];
    $this->subtitle = clienttranslate('Order 1 Unit + 2 On the Move');
    $t = clienttranslate(
      'When drawing a new Command card, draw two, choose one and discard the other.'
    );
    $this->texts = [
      [clienttranslate('Order 1 Unit Left + 2 On the Move'), $t],
      [clienttranslate('Order 1 unit Center + 2 On the Move'), $t],
      [clienttranslate('Order 1 unit Right + 2 On the Move'), $t],
    ];
  }
}
