<?php
namespace M44\Cards\Overlord;

class Ambush extends \M44\Cards\Standard\Ambush
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text[3] = clienttranslate('As soon as the Ambush is completed, draw 1 card back to replenish your hand.');
  }

  public function getPlayableSubSections($side = null)
  {
    return [6];
  }
}
