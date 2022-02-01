<?php
namespace M44\Cards\Breakthrough;

class MedicsAndMechanics extends \M44\Cards\Standard\MedicsAndMechanics
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text = [
      clienttranslate('Roll 4 battle dice.'),
      clienttranslate(
        'For each die matching a unit\'s symbol, you may return 1 lost figure to duty with that unit. For each star, you may return 1 lost figure to duty with a unit of your choice'
      ),
      clienttranslate(' unit may not gain more figures than it originally had.'),
      clienttranslate('If the unit recovers at least 1 figure, it may also be issued an order.'),
    ];
  }
}
