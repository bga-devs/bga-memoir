<?php
namespace M44\Cards\Overlord;

class MedicsAndMechanics extends \M44\Cards\Standard\MedicsAndMechanics
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text = [
    clienttranslate('Select 1 of your units that has suffered casualties. Roll 4 dice.'),
    clienttranslate('For each die matching the unit\'s symbol or star, return 1 lost figure to duty in that unit.'),
    clienttranslate('The unit may never recover more figures than it originally had. If the unit recovers at least 1 figure, it may be ordered.'),
  ];
  }

}
