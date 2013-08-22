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
}