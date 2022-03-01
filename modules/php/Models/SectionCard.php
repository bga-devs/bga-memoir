<?php
namespace M44\Models;
use M44\Managers\Players;
use M44\Managers\Units;
use M44\Helpers\Collection;

class SectionCard extends Card
{
  protected $subtitles = [];
  protected $texts = [];
  protected $sections = [];
  protected $nUnits = null;
  protected $nUnitsOnTheMove = null;
  protected $orderUnitsTitles = [];
  protected $nbFights = 1;

  public function getSubtitle()
  {
    return $this->subtitles[$this->value] ?? $this->subtitle;
  }

  public function getText()
  {
    return $this->texts[$this->value] ?? $this->text;
  }

  public function getSections()
  {
    if ($this->nUnits != null && empty($this->sections)) {
      $sections = [0, 0, 0];
      $sections[$this->value] = $this->nUnits;
      return $sections;
    } else {
      return $this->sections;
    }
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $units = new Collection();
    foreach ($this->getSections() as $i => $n) {
      if ($n > 0 || $this->nUnitsOnTheMove > 0) {
        $units = $units->merge($player->getUnitsInSection($i)->getPositions());
      }
    }

    return [
      'i18n' => ['desc'],
      'n' => $this->nUnits,
      'nTitle' => $this->nUnits,
      'nOnTheMove' => $this->nUnitsOnTheMove,
      'desc' => $this->orderUnitsTitles[$this->value] ?? '',
      'sections' => $this->getSections(),
      'units' => $units,
    ];
  }

  public function getArgsMoveUnits()
  {
    $player = $this->getPlayer();
    $units = Units::getActivatedByCard($this);

    return [
      'units' => $units->map(function ($unit) {
        return $unit->getPossibleMoves();
      }),
    ];
  }

  // ignoreFight => ignore number of fights (case of overrun)
  public function getArgsAttackUnits()
  {
    $player = $this->getPlayer();
    $units = Units::getActivatedByCard($this);

    /*
    // check if there is a unit already fighting
    $forceUnit = $units->filter(function ($unit) {
      return $unit->getFights() != 0 && $unit->getFights() < $this->nbFights;
    });

    if (count($forceUnit) != 0) {
      $id = $forceUnit->getIds()[0];
      return ['units' => [$id => $units[$id]->getTargetableUnits()]];
    }
*/

    return [
      'units' => $units->map(function ($unit) {
        if ($unit->getFights() >= $this->nbFights) {
          return [];
        }
        return $unit->getTargetableUnits();
      }),
    ];
  }

  public function getArgsArmorOverrun($unitId)
  {
    $unit = Units::get($unitId);
    if ($unit->getType() != ARMOR || $unit->getFights() > 1) {
      // TODO : this would break if a card allow an armor to fight twice
      return ['unit' => []];
    }

    return [
      'units' => [
        $unit->getId() => $unit->getTargetableUnits(),
      ],
    ];
  }

  public function updateDiceRoll($nDice)
  {
    // TODO: add cards (addition or replace)
    return $nDice;
  }
}
