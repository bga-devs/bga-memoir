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


  public function getArgsAttackUnits()
  {
    $player = $this->getPlayer();
    $units = Units::getActivatedByCard($this);

    return [
      'units' => $units->map(function ($unit) {
        return $unit->getTargetableUnits();
      }),
    ];
  }
}
