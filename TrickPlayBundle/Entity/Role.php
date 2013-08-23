<?php

namespace Talis\TrickPlayBundle\Entity;


use Talis\SwiftForumBundle\Model\Role as BaseRole;
use Talis\TrickPlayBundle\Entity\Character;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\RoleRepository")
 * @ORM\Table(name="roles")
 */
class Role extends BaseRole
{
    private static $SECURITY_LEVELS = array(
        "ROLE_BANNED" => 0,
        "ROLE_WARNED" => 1,
        "ROLE_GUEST" => 2,
        "ROLE_USER" => 3,
        "ROLE_MEMBER" => 4,
        "ROLE_OFFICER" => 5,
        "ROLE_MASTER" => 6,
        "ROLE_ADMIN" => 7,
        "ROLE_SUPER_ADMIN" => 8
    );

    /**
     * Is role greater than or equal to the specified security level?
     *
     * @param string $roleIdentifier
     * @return bool
     */
    public function isAtLeast($roleIdentifier)
    {
        $ownLevel = array_key_exists($this->getRole(), self::$SECURITY_LEVELS) ? self::$SECURITY_LEVELS[$this->getRole()] : -1;
        $targetLevel = array_key_exists($roleIdentifier, self::$SECURITY_LEVELS) ? self::$SECURITY_LEVELS[$roleIdentifier] : -1;

        return $ownLevel >= $targetLevel;
    }

    /**
     * Is role less than the specified security level?
     *
     * @param string $roleIdentifier
     * @return bool
     */
    public function isLessThan($roleIdentifier)
    {
        return !$this->isAtLeast($roleIdentifier);
    }
}
