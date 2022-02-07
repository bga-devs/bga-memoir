<?php
namespace M44\Models;
use M44\Managers\Players;
use M44\Managers\Troops;
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
    $troops = new Collection();
    foreach ($this->getSections() as $i => $n) {
      if ($n > 0 || $this->nUnitsOnTheMove > 0) {
        $troops = $troops->merge($player->getTroopsInSection($i)->getPositions());
      }
    }

    return [
      'i18n' => ['desc'],
      'n' => $this->nUnits,
      'nOnTheMove' => $this->nUnitsOnTheMove,
      'desc' => $this->orderUnitsTitles[$this->value] ?? '',
      'sections' => $this->getSections(),
      'troops' => $troops,
    ];
  }

  public function getArgsMoveUnits()
  {
    $player = $this->getPlayer();
    $troops = Troops::getActivatedByCard($this);

    return [
      'troops' => $troops->map(function ($troop) {
        return $troop->getPossibleMoves();
      }),
    ];
  }
}
