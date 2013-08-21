<?php

namespace Talis\TrickPlayBundle\Entity;


use Talis\SwiftForumBundle\Model\ForumCategory as BaseForumCategory;
use Talis\TrickPlayBundle\Entity\Character;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="forumCats")
 */
class ForumCategory extends BaseForumCategory
{
}