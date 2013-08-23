<?php

namespace Talis\TrickPlayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;

/**
 * FrontPage
 *
 * @ORM\Table(name="frontpages")
 * @ORM\Entity(repositoryClass="Talis\TrickPlayBundle\Entity\FrontPageRepository")
 */
class FrontPage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="markdown", type="text")
     */
    private $markdown;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\Column(name="lastEditor", type="integer")
     */
    private $lastEditor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastEdit", type="datetime")
     */
    private $lastEdit;

    /**
     * Get frontpage item
     * Returns a newly-instantiated (but not persisted) item if it does not already exist
     *
     * @return FrontPage
     */
    public static function get()
    {
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set markdown
     *
     * @param string $markdown
     * @param User $editor optional
     * @return FrontPage
     */
    public function setMarkdown($markdown, $editor)
    {
        $this->markdown = $markdown;
        $this->lastEdit = new DateTime('NOW');
        $this->lastEditor = $editor ? $editor->getId() : null;

        return $this;
    }

    /**
     * Get markdown
     * Defaults to empty string if not filled
     *
     * @return string
     */
    public function getMarkdown()
    {
        return $this->markdown ? $this->markdown : "";
    }

    /**
     * Get lastEditor
     *
     * @return integer
     */
    public function getLastEditor()
    {
        return $this->lastEditor;
    }

    /**
     * Get lastEdit
     *
     * @return \DateTime
     */
    public function getLastEdit()
    {
        return $this->lastEdit;
    }
}

class FrontPageRepository extends EntityRepository
{
    /**
     * Get frontpage item
     * Returns a newly-instantiated (but not persisted) item if it does not already exist
     *
     * @return FrontPage
     */
    public function get()
    {
        $query = "SELECT f FROM TalisTrickPlayBundle:FrontPage f";
        $firstItem = $this->getEntityManager()->createQuery($query)->setMaxResults(1)->getResult()[0];
        return $firstItem ? $firstItem : new FrontPage();
    }
}
