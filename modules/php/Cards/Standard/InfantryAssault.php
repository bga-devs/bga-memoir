<?php
namespace M44\Cards\Standard;

use M44\Managers\Units;

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

  public function getAdditionalPlayConstraints()
  {
    if ($this->isCounterAttack) {
      return null; // The card is already associated to a section
    }

    $player = $this->getPlayer();
    $sections = [];
    $infSections = [];
    $units = $player->getUnits();
    foreach ($units as $unit) {
      foreach ($unit->getSection() as $section) {
        if (!in_array($section, $sections)) {
          $sections[] = $section;
        }
        if ($unit->getType() == INFANTRY && !in_array($section, $infSections)) {
          $infSections[] = $section;
        }
      }
    }
    sort($sections);
    sort($infSections);

    return empty($infSections) ? $sections : $infSections;
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $section = (int) $this->extraDatas['section'];
    if($this->isCounterAttack){
      $section = $this->mirrorSection($section);
    }
    $units = $player->getUnitsInSection($section);

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

  public function getArgsMoveUnits()
  {
    $player = $this->getPlayer();
    $units = Units::getActivatedByCard($this);

    return [
      'units' => $units->map(function ($unit) {
        if ($unit->getType() == \INFANTRY) {
          return $unit->getPossibleMoves(3, 2);
        } else {
          return $unit->getPossibleMoves();
        }
      }),
    ];
  }
}
