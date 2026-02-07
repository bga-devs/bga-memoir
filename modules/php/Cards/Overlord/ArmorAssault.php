<?php
namespace M44\Cards\Overlord;

class ArmorAssault extends \M44\Cards\Standard\ArmorAssault
{
    public function getPlayableSubSections($side = null)
    {
        return [0,1,2,3,4,5];
    }
    
        
}
