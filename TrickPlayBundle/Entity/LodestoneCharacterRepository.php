<?php

namespace Talis\TrickPlayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;
use Talis\TrickPlayBundle\Lodestone;

class LodestoneCharacterRepository extends EntityRepository
{
    // Returns character data
    public function get($id)
    {
        $character = $this->findOneBy(array("id" => $id));
        return $character ? $character : new LodestoneCharacter();
    }
}
