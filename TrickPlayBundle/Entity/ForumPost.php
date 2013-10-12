<?php

namespace Talis\TrickPlayBundle\Entity;


use Talis\SwiftForumBundle\Model\ForumPost as BaseForumPost;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\ForumPostRepository")
 * @ORM\Table(name="forumPosts")
 */
class ForumPost extends BaseForumPost
{
}