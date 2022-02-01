<?php
namespace M44\Cards\Breakthrough;

class AirPower extends \M44\Cards\Standard\AirPower
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text[1] = clienttranslate('Roll 2 battle dice per hex, ignoring any terrain battle die reduction.');
  }
}
