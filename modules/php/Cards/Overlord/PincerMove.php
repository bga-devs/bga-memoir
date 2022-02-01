<?php
namespace M44\Cards\Overlord;

class PincerMove extends \M44\Cards\Standard\PincerMove
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitle = clienttranslate('Order 2 Units');
    $this->text = [
      clienttranslate('Left OR Right'),
      clienttranslate('Order 2 Units in one section OR the other'),
    ];
  }
}
