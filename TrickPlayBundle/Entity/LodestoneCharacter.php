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
        "blackmage" => array("thaumaturge", "archer"),
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
     * @ORM\ManyToOne(targetEntity="LodestoneFreeCompany", inversedBy="members")
     * @ORM\JoinColumn(name="freeCompany", referencedColumnName="id")
     */
    private $freeCompany;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="character")
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

    public function getUser()
    {
        return $this->user;
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

    // Only show highest level jobs
    public function getMainJobs()
    {
        $jobs = $this->getJobs();
        if (empty($jobs)) return array();

        $result = array();
        $highestLevel = null;

        foreach ($jobs as $name => $level) {
            if (!$highestLevel) $highestLevel = $level;
            if ($level >= $highestLevel) $result[$name] = $level;
        }

        return $result;
    }

    // Only show non-main jobs
    public function getSecondaryJobs()
    {
        $jobs = $this->getJobs();
        $mainJobs = $this->getMainJobs();
        return array_diff($jobs, $mainJobs);
    }

    // Only show non-main professions
    public function getSecondaryProfessions()
    {
        $professions = $this->getProfessions();
        $mainProfessions = $this->getMainProfessions();
        return array_diff($professions, $mainProfessions);
    }

    // Only show highest level professions
    public function getMainProfessions()
    {
        $professions = $this->getProfessions();
        if (empty($professions)) return array();

        $result = array();
        $highestLevel = null;

        foreach ($professions as $name => $level) {
            if (!$highestLevel) $highestLevel = $level;
            if ($level >= $highestLevel) $result[$name] = $level;
        }

        return $result;
    }

    // Show jobs, sorted by highest to lowest
    public function getJobs()
    {
        $classes = $this->getClasses();
        $jobs = array();

        foreach (self::$JOB_REQUIREMENTS as $name => $requirement) {
            $main = $this->$requirement[0];
            $secondary = $this->$requirement[1];
            $level = ($main >= 30 && $secondary >= 15) ? $main : null;
            if ($level) $jobs[$name] = $level;
        }

        arsort($jobs);
        return $jobs;
    }

    // Show classes, sorted by highest to lowest
    public function getClasses()
    {
        $classes = array();
        foreach (self::$CLASSES as $name) {
            if ($this->$name) $classes[$name] = $this->$name;
        }

        arsort($classes);
        return $classes;
    }

    // Show professions, sorted by highest to lowest
    public function getProfessions()
    {
        $professions = array();
        foreach (self::$PROFESSIONS as $name) {
            if ($this->$name) $professions[$name] = $this->$name;
        }

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
