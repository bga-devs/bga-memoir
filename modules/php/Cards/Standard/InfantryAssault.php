<?php
namespace M44\Cards\Standard;

use M44\Managers\Units;
use M44\Core\Globals;

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
    if ($this->isCounterAttackMirror) {
      return null; // The card is already associated to a section
    }

    $player = $this->getPlayer();
    $sections = [];
    $infSections = [];
    $units = $player->getUnits();
    foreach ($units as $unit) {
      foreach ($unit->getSections() as $section) {
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
    $marineCommand = $player->isMarineCommand();
    $section = (int) $this->extraDatas['section'];
    if ($this->isCounterAttackMirror) {
      $section = $this->mirrorSection($section);
    }
    $units = $player->getUnitsInSection($section);

    // Keep only infantry
    $infantry = $units->filter(function ($unit) {
      return $unit->getType() == \INFANTRY;
    });

    if ($infantry->empty()) {
      // No infantry => 1 unit of your choice
      $unitstmp = $units->filter(function ($unit) {
        return (!($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn()));
      });
      return [
        'i18n' => ['desc'],
        'n' => $marineCommand ? 2 : 1,
        'nTitle' => $marineCommand ? 2 : 1,
        'desc' => \clienttranslate('(because no infantry units)'),
        'units' => $unitstmp->getPositions(),
      ];
    } else {
      $descs = [
        clienttranslate('on the Left Flank (infantry units only)'),
        clienttranslate('in the Center (infantry units only)'),
        clienttranslate('on the Right Flank (infantry units only)'),
      ];
      $infantrytmp = $infantry->filter(function ($unit) {
        return (!($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn()));
      });

      return [
        'i18n' => ['desc', 'nTitle'],
        'n' => \INFINITY,
        'nTitle' => \clienttranslate('all'),
        'desc' => $descs[$section],
        'units' => $infantrytmp->getPositions(),
      ];
    }
  }
}
