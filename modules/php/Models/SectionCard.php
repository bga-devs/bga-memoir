<?php
namespace M44\Models;
use M44\Managers\Players;
use M44\Managers\Units;
use M44\Helpers\Collection;
use M44\Core\Globals;

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

  public function getSections($isMarineCommand)
  {
    $sections = [0, 0, 0];
    if ($this->nUnits != null && empty($this->sections)) {
      $sections[$this->value] = $this->nUnits;
    } else {
      $sections = $this->sections;
    }

    if ($isMarineCommand) {
      foreach ($sections as &$s) {
        $s++;
      }
    }

    return $this->isCounterAttackMirror ? array_reverse($sections) : $sections;
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $marineCommand = $player->isMarineCommand();

    $units = new Collection();
    $sectionId = $this->getExtraDatas('section');
    if ($this->isCounterAttackMirror) {
      $sectionId = $this->mirrorSection($sectionId);
    }

    if ($sectionId != null) {
      if ($this->nUnits > 0 || $this->nUnitsOnTheMove > 0) {
        $units = $units->merge($player->getUnitsInSection($sectionId)->getPositions());
      }
    } else {
      foreach ($this->getSections($marineCommand) as $i => $n) {
        if ($this->nUnits > 0 || $this->nUnitsOnTheMove > 0) {
          $units = $units->merge($player->getUnitsInSection($i)->getPositions());
        }
      }
    }

    $val = $this->isCounterAttackMirror ? 2 - $this->value : $this->value;
    $nbUnits = $marineCommand ? $this->nUnits + 1 : $this->nUnits;
    $nbOnTheMove =
      $this->nUnitsOnTheMove == 0 ? 0 : ($marineCommand ? $this->nUnitsOnTheMove + 1 : $this->nUnitsOnTheMove);
    return [
      'i18n' => ['desc'],
      'n' => $nbUnits,
      'nTitle' => $nbUnits,
      'nOnTheMove' => $nbOnTheMove,
      'desc' => $this->orderUnitsTitles[$val] ?? '',
      'sections' => $this->getSections($marineCommand),
      'units' => $units,
    ];
  }
}
