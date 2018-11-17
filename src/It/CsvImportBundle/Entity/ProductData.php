<?php

namespace It\CsvImportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="It\CsvImportBundle\Entity\ProductDataRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductData
{
    /**
     * @var integer
     *
     * @ORM\Column(name="intProductDataId", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="strProductName", type="string", length=50)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(name="strProductDesc", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $desc;

    /**
     * @ORM\Column(name="strProductCode", type="string", length=10, unique=true)
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @var \DateTime
     * @ORM\Column(name="dtmAdded", type="time", nullable=true)
     */
    private $added;

    /**
     * @var \DateTime
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private $discontinued;

    /**
     * @ORM\Version()
     * @var \DateTime
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @ORM\Column(name="decStockLevel", type="decimal", precision=8, scale=2)
     */
    private $stockLevel = 0;

    /**
     * @ORM\Column(name="decPrice", type="decimal", precision=8, scale=2)
     */
    private $price = 0;


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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return \DateTime
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param \DateTime $added
     */
    public function setAdded($added)
    {
        $this->added = $added;
    }

    /**
     * @return \DateTime
     */
    public function getDiscontinued()
    {
        return $this->discontinued;
    }

    /**
     * @param \DateTime $discontinued
     */
    public function setDiscontinued($discontinued)
    {
        $this->discontinued = $discontinued;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }


    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getStockLevel()
    {
        return $this->stockLevel;
    }

    /**
     * @param $stockLevel float
     */
    public function setStockLevel($stockLevel)
    {
        $this->stockLevel = $stockLevel;
    }
}
