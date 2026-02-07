<?php
namespace M44\Cards\Overlord;

class ArtilleryBombard extends \M44\Cards\Standard\ArtilleryBombard
{
    public function getPlayableSubSections($side = null)
    {
        return [0,1,2,3,4,5];
    }
}
