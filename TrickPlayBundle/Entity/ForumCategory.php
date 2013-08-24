<?php

namespace Talis\TrickPlayBundle\Entity;


use Talis\SwiftForumBundle\Model\ForumCategory as BaseForumCategory;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\ForumCategoryRepository")
 * @ORM\Table(name="forumCats")
 */
class ForumCategory extends BaseForumCategory
{
}