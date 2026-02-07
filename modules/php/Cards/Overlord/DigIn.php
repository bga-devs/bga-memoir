<?php
namespace M44\Cards\Overlord;

class DigIn extends \M44\Cards\Standard\DigIn
{
    public function getPlayableSubSections($side = null)
    {
        return [0,1,2,3,4,5];
    }
}
