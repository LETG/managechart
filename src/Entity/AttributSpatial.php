<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AttributSpatial
 *
 * @ORM\Table(name="attribut_spatial")
 * @ORM\Entity(repositoryClass="App\Repository\AttributSpatialRepository")
 */
class AttributSpatial
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nameAttribut", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nameAttribut;

    /**
     * @var string
     *
     * @ORM\Column(name="valueAttribut", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $valueAttribut;

    /**
     * @var string
     *
     * @ORM\Column(name="keywordAttribut", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $keywordAttribut;

    /**
     * @var string
     *
     * @ORM\Column(name="typeAttribut", type="string", length=255)
     */
    private $typeAttribut;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataList", inversedBy="attributsSpatiaux")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $dataList;


    /*
     * @ORM\PreRemove
     */
    public function updateDataList() {
        $this->dataList->removeAttributsSpatiaux($this);
    }

    /**
     * Set dataList
     *
     * @param \App\Entity\DataList $dataList
     * @return AttributSpatial
     */
    public function setDataList(\App\Entity\DataList $dataList) {
        $this->dataList = $dataList;
        $this->dataList->addAttributsSpatiaux($this);

        return $this;
    }

    /**
     * Get dataList
     *
     * @return \App\Entity\DataList
     */
    public function getDataList() {
        return $this->dataList;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nameAttribut
     *
     * @param string $nameAttribut
     *
     * @return AttributSpatial
     */
    public function setNameAttribut($nameAttribut)
    {
        $this->nameAttribut = $nameAttribut;

        return $this;
    }

    /**
     * Get nameAttribut
     *
     * @return string
     */
    public function getNameAttribut()
    {
        return $this->nameAttribut;
    }

    /**
     * Set valueAttribut
     *
     * @param string $valueAttribut
     *
     * @return AttributSpatial
     */
    public function setValueAttribut($valueAttribut)
    {
        $this->valueAttribut = $valueAttribut;

        return $this;
    }

    /**
     * Get valueAttribut
     *
     * @return string
     */
    public function getValueAttribut()
    {
        return $this->valueAttribut;
    }

    /**
     * Set keywordAttribut
     *
     * @param string $keywordAttribut
     *
     * @return AttributSpatial
     */
    public function setKeywordAttribut($keywordAttribut)
    {
        $this->keywordAttribut = $keywordAttribut;

        return $this;
    }

    /**
     * Get keywordAttribut
     *
     * @return string
     */
    public function getKeywordAttribut()
    {
        return $this->keywordAttribut;
    }

    /**
     * Set typeAttribut
     *
     * @param string $typeAttribut
     *
     * @return AttributSpatial
     */
    public function setTypeAttribut($typeAttribut)
    {
        $this->typeAttribut = $typeAttribut;

        return $this;
    }

    /**
     * Get typeAttribut
     *
     * @return string
     */
    public function getTypeAttribut()
    {
        return $this->typeAttribut;
    }
}

