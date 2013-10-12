<?php

namespace Talis\TrickPlayBundle\Entity;


use Talis\SwiftForumBundle\Model\ForumTopic as BaseForumTopic;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\ForumTopicRepository")
 * @ORM\Table(name="forumTopics")
 */
class ForumTopic extends BaseForumTopic
{
}
