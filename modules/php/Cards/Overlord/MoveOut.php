<?php
namespace M44\Cards\Overlord;

class MoveOut extends \M44\Cards\Standard\MoveOut
{
    public function getPlayableSubSections($side = null)
    {
        return [0,1,2,3,4,5];
    }
}
