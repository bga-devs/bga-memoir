<?php
namespace M44\Cards\Overlord;

class DirectFromHQ extends \M44\Cards\Standard\DirectFromHQ
{
    public function getPlayableSubSections($side = null)
    {
        return [0,1,2,3,4,5];
    }
}
