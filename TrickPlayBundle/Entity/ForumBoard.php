<?php

namespace Talis\TrickPlayBundle\Entity;


use Talis\SwiftForumBundle\Model\ForumBoard as BaseForumBoard;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\ForumBoardRepository")
 * @ORM\Table(name="forumBoards")
 */
class ForumBoard extends BaseForumBoard
{
}
