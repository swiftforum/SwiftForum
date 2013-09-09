<?php

namespace Talis\TrickPlayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;

/**
 * LodestoneFreeCompany
 *
 * @ORM\Table(name="lodestonefreecompanies")
 * @ORM\Entity(repositoryClass="Talis\TrickPlayBundle\Entity\LodestoneFreeCompanyRepository")
 */
class LodestoneFreeCompany
{
    /**
     * This is a string because Lodestone IDs can be arbitrarily long
     * @ORM\Column(name="id", type="string", length=30)
     * @ORM\Id()
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=120)
     */
    private $picture;

    /**
     * @ORM\Column(name="tag", type="string", length=20)
     */
    private $tag;

    /**
     * @ORM\Column(name="slogan", type="string", length=60)
     */
    private $slogan;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUpdated", type="datetime")
     */
    private $lastUpdated;

    /**
     * @ORM\OneToMany(targetEntity="LodestoneCharacter", mappedBy="freeCompany")
     */
    private $members;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSlogan()
    {
        return $this->slogan;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function getUrl()
    {
        return "http://na.finalfantasyxiv.com/lodestone/freecompany/" . urlencode($this->id);
    }

    public function getMembers()
    {
        return $this->members;
    }

    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function set($params)
    {
        $PROPERTIES = array("id", "name", "tag", "slogan", "picture");
        $this->lastUpdated = new \DateTime("now");

        foreach ($PROPERTIES as $name) {
            if (isset($params[$name])) $this->$name = $params[$name];
        }
    }
}
