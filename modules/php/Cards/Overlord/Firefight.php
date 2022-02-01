<?php
namespace M44\Cards\Overlord;

class Firefight extends \M44\Cards\Standard\Firefight
{
  public function __construct($row)
  {
    parent::__construct($row);
    unset($this->text[2]);
  }
}
