<?php

namespace Talis\TrickPlayBundle\Entity;


use Talis\SwiftForumBundle\Model\Icons as BaseIcon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\IconsRepository")
 * @ORM\Table(name="icons")
 */
class Icons extends BaseIcon
{
}