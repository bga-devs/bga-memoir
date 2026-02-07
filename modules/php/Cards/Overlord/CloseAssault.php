<?php
namespace M44\Cards\Overlord;

class CloseAssault extends \M44\Cards\Standard\CloseAssault
{
    public function getPlayableSubSections($side = null)
    {
        return [0,1,2,3,4,5];
    }
}
