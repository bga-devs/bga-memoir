<?php
namespace M44\Models;
use M44\Managers\Players;

class SectionCard implements Card
{
  protected $subtitles = [];
  protected $texts = [];
  protected $sections = [];
  protected $nUnits = null;

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
    if ($this->nUnits != null) {
      $sections = [0, 0, 0];
      $sections[$this->value] = $this->nUnits;
      return $sections;
    } else {
      return $this->sections;
    }
  }
}
