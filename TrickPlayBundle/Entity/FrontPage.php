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
     * @ORM\JoinColumn(name="lastEditor", referencedColumnName="id")
     */
    private $lastEditor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastEdit", type="datetime")
     */
    private $lastEdit;

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
        $this->lastEdit = new \DateTime("now");
        $this->lastEditor = $editor;

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
