<?php
namespace M44\Cards\Breakthrough;

class PincerMove extends \M44\Cards\Standard\PincerMove
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitle = clienttranslate('Order 2 Units on each Flank');
    $this->text = [clienttranslate('Order 2 Units on the Left Flank and 2 Units on the Right Flank')];
  }
}
