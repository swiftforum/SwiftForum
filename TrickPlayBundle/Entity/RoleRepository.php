<?php

namespace Talis\TrickPlayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talis\SwiftForumBundle\Model\RoleRepository as BaseRepository;

class RoleRepository extends BaseRepository
{
    /**
     * Get role by security identifier (e.g. ROLE_ADMIN)
     * @param string $identifier
     * @return Role
     */
    public function getByIdentifier($identifier)
    {
        $query = "SELECT r FROM TalisTrickPlayBundle:Role r WHERE r.role = :identifier";
        $results =  $this->getEntityManager()->createQuery($query)->setParameter("identifier", $identifier)->setMaxResults(1)->getResult();
        return empty($results) ? null : $results[0];
    }
}
