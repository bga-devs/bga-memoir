<?php
namespace M44\Cards\Breakthrough;

class ReconInForce extends \M44\Cards\Standard\ReconInForce
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitle = clienttranslate('Order 1 Unit in each section');
    $this->text = [clienttranslate('Order 1 Unit in each section')];
  }
}
