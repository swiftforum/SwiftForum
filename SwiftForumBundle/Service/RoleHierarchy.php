<?php

namespace Talis\SwiftForumBundle\Service;

use Symfony\Component\Security\Core\Role\RoleHierarchy as SymfonyRoleHierarchy;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\Role\Role;

class RoleHierarchy extends SymfonyRoleHierarchy
{
    private $hierarchy;
    private $map;

    /**
     * Constructor.
     *
     * @param array $hierarchy An array defining the hierarchy
     */
    public function __construct(array $hierarchy)
    {
        $this->hierarchy = $hierarchy;

        $this->buildRoleMap();
    }

    /**
     * Returns an array of all roles reachable by the given ones.
     *
     * @param RoleInterface[] $roles An array of RoleInterface instances
     *
     * @return RoleInterface[] An array of RoleInterface instances
     */
    public function getReachableRoles(array $roles)
    {
        $reachableRoles = $roles;
        foreach ($roles as $role) {
            if (!isset($this->map[$role->getRole()])) {
                continue;
            }

            foreach ($this->map[$role->getRole()] as $r) {
                $reachableRoles[] = new Role($r);
            }
        }

        return $reachableRoles;
    }

    private function buildRoleMap()
    {
        $this->map = array();
        foreach ($this->hierarchy as $main => $roles) {
            $this->map[$main] = $roles;
            $visited = array();
            $additionalRoles = $roles;
            while ($role = array_shift($additionalRoles)) {
                if (!isset($this->hierarchy[$role])) {
                    continue;
                }

                $visited[] = $role;
                $this->map[$main] = array_unique(array_merge($this->map[$main], $this->hierarchy[$role]));
                $additionalRoles = array_merge($additionalRoles, array_diff($this->hierarchy[$role], $visited));
            }
        }
    }

    public function getMap()
    {
        return $this->map;
    }

    public function getPermittedMap($role)
    {
        $permitted = array();

        if(isset($this->map[$role])) {
            $permitted = $this->map[$role];
        }
        $permitted[] = 'ROLE_WARNED';
        $permitted[] = 'ROLE_BANNED';
        return $permitted;
    }

} 