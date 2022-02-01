<?php
namespace M44\Cards\Overlord;

class Recon extends \M44\Cards\Standard\Recon
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitles = [];
    $this->subtitle = clienttranslate('Order 1 Unit');
    $t = clienttranslate(
      'At the end of this turn, your Commander-in-Chief draws 3 cards, not 2, and keeps them all, up to his maximum hand size, as dictated by the scenario.'
    );
    $this->texts = [
      [clienttranslate('Order 1 Unit on the Left'), $t],
      [clienttranslate('Order 1 unit in the Center'), $t],
      [clienttranslate('Order 1 unit on the Right'), $t],
    ];
  }
}
