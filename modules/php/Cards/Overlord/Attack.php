<?php
namespace M44\Cards\Overlord;

class Attack extends \M44\Cards\Standard\Attack
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitles = [];
    $this->subtitle = clienttranslate('Order 3 Units');
    $this->texts = [
      [clienttranslate('Order 3 Units on the Left in one section OR the other')],
      [clienttranslate('Order 3 Units in the Center in one section OR the other')],
      [clienttranslate('Order 3 Units on the Right in one section OR the other')],
    ];
  }
}
