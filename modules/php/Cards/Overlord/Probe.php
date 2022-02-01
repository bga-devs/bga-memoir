<?php
namespace M44\Cards\Overlord;

class Probe extends \M44\Cards\Standard\Probe
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitles = [];
    $this->subtitle = clienttranslate('Order 2 Units');
    $this->texts = [
      [clienttranslate('Order 2 Units on the Left in one section OR the other')],
      [clienttranslate('Order 2 Units in the Center in one section OR the other')],
      [clienttranslate('Order 2 Units on the Right in one section OR the other')],
    ];
  }
}
