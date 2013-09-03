<?php

namespace Talis\TrickPlayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;

/**
 * LodestoneCharacter
 *
 * @ORM\Table(name="lodestonecharacters")
 * @ORM\Entity(repositoryClass="Talis\TrickPlayBundle\Entity\LodestoneCharacterRepository")
 */
class LodestoneCharacter
{
    public static $CLASSES = array("archer", "pugilist", "lancer", "gladiator", "marauder", "thaumaturge", "arcanist", "conjurer");
    public static $PROFESSIONS = array("alchemist", "armorer", "blacksmith", "carpenter", "culinarian", "goldsmith", "leatherworker", "weaver", "botanist", "fisher" ,"miner");
    public static $JOB_REQUIREMENTS = array(
        "bard" => array("archer", "pugilist"),
        "dragoon" => array("lancer", "marauder"),
        "monk" => array("pugilist", "lancer"),
        "paladin" => array("gladiator", "conjurer"),
        "warrior" => array("marauder", "gladiator"),
        "blackmage" => array("thaumaturge", "arcanist"),
        "scholar" => array("arcanist", "conjurer"),
        "summoner" => array("arcanist", "thaumaturge"),
        "whitemage" => array("conjurer", "arcanist")
    );

    /**
     * This is a string because Lodestone IDs can be arbitrarily long
     * @ORM\Column(name="id", type="string", length=30)
     * @ORM\Id()
     */
    private $id;

    /**
     * This is a string because Lodestone IDs can be arbitrarily long
     * @ORM\ManyToOne(targetEntity="LodestoneFreeCompany")
     * @ORM\JoinColumn(name="freeCompany", referencedColumnName="id")
     */
    private $freeCompany;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=60)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=120)
     */
    private $picture;

    /**
     * @var integer
     *
     * @ORM\Column(name="gladiator", type="integer", nullable=true)
     */
    private $gladiator;

    /**
     * @var integer
     *
     * @ORM\Column(name="marauder", type="integer", nullable=true)
     */
    private $marauder;

    /**
     * @var integer
     *
     * @ORM\Column(name="archer", type="integer", nullable=true)
     */
    private $archer;

    /**
     * @var integer
     *
     * @ORM\Column(name="pugilist", type="integer", nullable=true)
     */
    private $pugilist;

    /**
     * @var integer
     *
     * @ORM\Column(name="lancer", type="integer", nullable=true)
     */
    private $lancer;

    /**
     * @var integer
     *
     * @ORM\Column(name="conjurer", type="integer", nullable=true)
     */
    private $conjurer;

    /**
     * @var integer
     *
     * @ORM\Column(name="thaumaturge", type="integer", nullable=true)
     */
    private $thaumaturge;

    /**
     * @var integer
     *
     * @ORM\Column(name="arcanist", type="integer", nullable=true)
     */
    private $arcanist;

    /**
     * @var integer
     *
     * @ORM\Column(name="carpenter", type="integer", nullable=true)
     */
    private $carpenter;

    /**
     * @var integer
     *
     * @ORM\Column(name="armorer", type="integer", nullable=true)
     */
    private $armorer;

    /**
     * @var integer
     *
     * @ORM\Column(name="leatherworker", type="integer", nullable=true)
     */
    private $leatherworker;

    /**
     * @var integer
     *
     * @ORM\Column(name="alchemist", type="integer", nullable=true)
     */
    private $alchemist;

    /**
     * @var integer
     *
     * @ORM\Column(name="blacksmith", type="integer", nullable=true)
     */
    private $blacksmith;

    /**
     * @var integer
     *
     * @ORM\Column(name="goldsmith", type="integer", nullable=true)
     */
    private $goldsmith;

    /**
     * @var integer
     *
     * @ORM\Column(name="weaver", type="integer", nullable=true)
     */
    private $weaver;

    /**
     * @var integer
     *
     * @ORM\Column(name="culinarian", type="integer", nullable=true)
     */
    private $culinarian;

    /**
     * @var integer
     *
     * @ORM\Column(name="miner", type="integer", nullable=true)
     */
    private $miner;

    /**
     * @var integer
     *
     * @ORM\Column(name="fisher", type="integer", nullable=true)
     */
    private $fisher;

    /**
     * @var integer
     *
     * @ORM\Column(name="botanist", type="integer", nullable=true)
     */
    private $botanist;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUpdated", type="datetime", nullable=true)
     */
    private $lastUpdated;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function getUrl()
    {
        return "http://na.finalfantasyxiv.com/lodestone/character/" . urlencode($this->id);
    }

    public function getJobs()
    {
        $classes = $this->getClasses();

        $REQUIREMENTS = array(
            "bard" => array("archer", "pugilist"),
            "dragoon" => array("lancer", "marauder"),
            "monk" => array("pugilist", "lancer"),
            "paladin" => array("gladiator", "conjurer"),
            "warrior" => array("marauder", "gladiator"),
            "blackmage" => array("thaumaturge", "arcanist"),
            "scholar" => array("arcanist", "conjurer"),
            "summoner" => array("arcanist", "thaumaturge"),
            "whitemage" => array("conjurer", "arcanist")
        );

        $jobs = (array_map(function($requirement)
        {
            $main = $this[$requirement[0]];
            $secondary = $this[$requirement[1]];
            return ($main >= 30 && $secondary >= 15) ? $main : null;
        }, $REQUIREMENTS));

        arsort($jobs);
        return $jobs;
    }

    public function getClasses()
    {
        $classes = array_map(function($name) { return $this[$name]; }, self::$CLASSES);
        arsort($classes);
        return $classes;
    }

    public function getProfessions()
    {
        $professions = array_map(function($name) { return $this[$name]; }, self::$PROFESSIONS);
        arsort($professions);
        return $professions;
    }

    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setFreeCompany($company)
    {
        $this->freeCompany = $company;
    }

    public function set($params, $updateTimestamp = true)
    {
        $properties = array("id", "name", "picture");
        $properties = array_merge($properties, self::$CLASSES, self::$PROFESSIONS);

        if ($updateTimestamp) $this->lastUpdated = new \DateTime("now");

        foreach ($properties as $name) {
            if (isset($params[$name])) $this->$name = $params[$name];
        }
    }
}
