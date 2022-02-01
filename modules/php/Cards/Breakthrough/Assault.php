<?php
namespace M44\Cards\Breakthrough;

class Assault extends \M44\Cards\Standard\Assault
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitles = [];
    $this->subtitle = clienttranslate('Order ALL Units');
    $this->texts = [
      [clienttranslate('Order All Units on the Left Flank')],
      [clienttranslate('Order All Units in the Center')],
      [clienttranslate('Order All Units on the Right Flank')],
    ];
  }
}
