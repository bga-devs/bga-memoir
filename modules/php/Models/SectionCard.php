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
    $sections = [0, 0, 0];
    if ($this->nUnits != null && empty($this->sections)) {
      $sections[$this->value] = $this->nUnits;
    } else {
      $sections = $this->sections;
    }

    return $this->isCounterAttack ? array_reverse($sections) : $sections;
  }

  public function getArgsOrderUnits($pId = false)
  {
    if ($pId == false) {
      $player = $this->getPlayer();
    } else {
      // used for counter attack
      $player = Players::get($pId);
    }

    $units = new Collection();
    $sectionId = $this->getExtraDatas('section');
    if ($this->isCounterAttack) {
      $sectionId = $this->mirrorSection($sectionId);
    }

    if ($sectionId != null) {
      if ($n > 0 || $this->nUnitsOnTheMove > 0) {
        $units = $units->merge($player->getUnitsInSection($sectionId)->getPositions());
      }
    } else {
      foreach ($this->getSections() as $i => $n) {
        if ($n > 0 || $this->nUnitsOnTheMove > 0) {
          $units = $units->merge($player->getUnitsInSection($i)->getPositions());
        }
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
}
