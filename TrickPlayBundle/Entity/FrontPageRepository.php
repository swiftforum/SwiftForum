<?php

namespace Talis\TrickPlayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;

class FrontPageRepository extends EntityRepository
{
    /**
     * Get frontpage item
     * Returns a newly-instantiated (but not persisted) item if it does not already exist
     *
     * @return FrontPage
     */
    public function get()
    {
        $query = "SELECT f FROM TalisTrickPlayBundle:FrontPage f";
        $items = $this->getEntityManager()->createQuery($query)->setMaxResults(1)->getResult();
        return isset($items[0]) ? $items[0] : new FrontPage();
    }
}
