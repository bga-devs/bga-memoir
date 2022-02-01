<?php
namespace M44\Cards\Overlord;

class Assault extends \M44\Cards\Standard\Assault
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitles = [];
    $this->subtitle = clienttranslate('Order ALL Units');
    $this->texts = [
      [clienttranslate('Order All Units on the Left in one section OR the other')],
      [clienttranslate('Order All Units in the Center in one section OR the other')],
      [clienttranslate('Order All Units on the Right in one section OR the other')],
    ];
  }
}
