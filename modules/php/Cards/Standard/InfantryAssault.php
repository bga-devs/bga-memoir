<?php
namespace M44\Cards\Standard;

class InfantryAssault extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_INFANTRY_ASSAULT;
    $this->name = clienttranslate('Infantry Assault');
    $this->text = [
      clienttranslate(
        'Issue an order to all <INFANTRY> units in 1 section. Units may move up to 2 hexes and still battle, or move 3 hexes but not battle.'
      ),
      clienttranslate('Terrain movement and battle restrictions still apply.'),
      clienttranslate('If you do not command any infantry units, issue an order to 1 unit of your choice.'),
    ];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $units = $player->getUnits();

    // Keep only armor
    $infantry = $units->filter(function ($unit) {
      return $unit->getType() == \INFANTRY;
    });

    if ($infantry->empty()) {
      // No infantry => 1 unit of your choice
      return [
        'i18n' => ['desc'],
        'n' => 1,
        'nTitle' => 1,
        'desc' => \clienttranslate('(because no infantry units)'),
        'units' => $units,
      ];
    } else {
      return [
        'i18n' => ['desc'],
        'n' => \INFINITY,
        'nTitle' => \INFINITY,
        'desc' => \clienttranslate('(infantry units only)'),
        'units' => $infantry,
      ];
    }
  }

  public function getAdditionalPlayConstraints()
  {
    $args = $this->getArgsOrderUnits();
    $sections = [];
    if ($args['n'] == \INFINITY) {
      foreach ($args['units'] as $unit) {
        foreach ($unit->getSection() as $section) {
          $sections[] = $section;
        }
      }
    }
    $sections = array_values(\array_unique($sections));
    if (count($sections) == 0) {
      return null;
    } else {
      return $sections;
    }
  }
}
