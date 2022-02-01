<?php
namespace M44\Cards\Overlord;

class CounterAttack extends \M44\Cards\Standard\CounterAttack
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text = [
      clienttranslate('Issue the same order just played by your oppposing Field General'),
      clienttranslate('The units your order must be in the section(s) directly opposite to the one(s) your opponent ordered his units in.'),
      clienttranslate('When countering a card played by your opposing Commander-In-Chief, you may use the same tactic, applied to any section of the battlefield.'),
    ];
  }
}
