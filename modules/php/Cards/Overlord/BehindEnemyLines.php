<?php
namespace M44\Cards\Overlord;

class BehindEnemyLines extends \M44\Cards\Standard\BehindEnemyLines
{
    public function getPlayableSubSections($side = null)
    {
        return [0,1,2,3,4,5];
    }
}
