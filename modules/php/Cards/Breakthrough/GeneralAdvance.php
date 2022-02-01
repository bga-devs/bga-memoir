<?php
namespace M44\Cards\Breakthrough;

class GeneralAdvance extends \M44\Cards\Standard\GeneralAdvance
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->subtitle = clienttranslate('Order 2 Units in each section');
    $this->text = [clienttranslate('Order 2 Units in each section')];
  }
}
