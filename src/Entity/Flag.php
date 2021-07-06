<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Flag
 *
 * @ORM\Table(name="flag")
 * @ORM\Entity(repositoryClass="App\Repository\FlagRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Flag
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
     * @ORM\ManyToOne(targetEntity="App\Entity\DataList", inversedBy="flag")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $dataList;


    /**
     * @var string
     *
     * @ORM\Column(name="parameterDataList", type="string", length=255, nullable=true)
     */
    private $parameterDataList;

    /**
     * @var string
     *
     * @ORM\Column(name="onseries", type="string", length=255, nullable=true)
     */
    private $onseries;

    /**
     * @var string
     *
     * @ORM\Column(name="titleFlag", type="string", length=255, nullable=true)
     */
    private $titleFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="shapeflag", type="string", length=255, nullable=false)
     */
    private $shapeflag;

    /**
     * @var string
     *
     * @ORM\Column(name="colorflag", type="string", length=255, nullable=false)
     */
    private $colorflag;

    /**
     * @var string
     *
     * @ORM\Column(name="widthflag", type="string", length=255, nullable=true)
     */
    private $widthflag;

    /**
     * @var string
     *
     * @ORM\Column(name="styleflag", type="string", length=255, nullable=true)
     */
    private $styleflag;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\YAxis", inversedBy="flag")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $yAxis;

    /**
     * @var int
     *
     * @ORM\Column(name="yaxisOrder", type="integer", nullable=true)
     */
    private $yaxisOrder;

    /**
     * Set yaxisOrder
     *
     * @param integer $yaxisOrder
     *
     * @return Flag
     */
    public function setYaxisOrder($yaxisOrder)
    {
        $this->yaxisOrder = $yaxisOrder;

        return $this;
    }

    /**
     * Get yaxisOrder
     *
     * @return int
     */
    public function getYaxisOrder()
    {
        return $this->yaxisOrder;
    }

    /*
     * @ORM\PreRemove
     */
    public function updateYAxis() {
        $this->yAxis->removeFlag($this);
    }

    /*
     * @ORM\PreRemove
     */
    public function updateDataList() {
        $this->dataList->removeFlag($this);
    }

    /**
     * Set yAxis
     *
     * @param \App\Entity\YAxis $yAxis
     * @return Flag
     */
    public function setYAxis(\App\Entity\YAxis $yAxis) {
        $this->yAxis = $yAxis;
        $this->yAxis->addFlag($this);

        return $this;
    }

    /**
     * Get yAxis
     *
     * @return \App\Entity\YAxis 
     */
    public function getYAxis() {
        return $this->yAxis;
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
     * Set dataList
     *
     * @param \App\Entity\DataList $dataList
     * @return Flag
     */
    public function setDataList(\App\Entity\DataList $dataList) {
        $this->dataList = $dataList;

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
     * Set titleFlag
     *
     * @param string $titleFlag
     * @return Flag
     */
    public function setTitleFlag($titleFlag)
    {
        $this->titleFlag = $titleFlag;

        return $this;
    }

    /**
     * Get titleFlag
     *
     * @return string
     */
    public function getTitleFlag()
    {
        return $this->titleFlag;
    }

    /**
     * Set parameterDataList
     *
     * @param string $parameterDataList
     * @return Flag
     */
    public function setParameterDataList($parameterDataList)
    {
        $this->parameterDataList = $parameterDataList;

        return $this;
    }

    /**
     * Get parameterDataList
     *
     * @return string
     */
    public function getParameterDataList()
    {
        return $this->parameterDataList;
    }

    /**
     * Set onseries
     *
     * @param string $onseries
     *
     * @return Flag
     */
    public function setOnseries($onseries)
    {
        $this->onseries = $onseries;

        return $this;
    }

    /**
     * Get onseries
     *
     * @return string
     */
    public function getOnseries()
    {
        return $this->onseries;
    }

    /**
     * Set shapeflag
     *
     * @param string $shapeflag
     *
     * @return Flag
     */
    public function setShapeflag($shapeflag)
    {
        $this->shapeflag = $shapeflag;

        return $this;
    }

    /**
     * Get shapeflag
     *
     * @return string
     */
    public function getShapeflag()
    {
        return $this->shapeflag;
    }

    /**
     * Set colorflag
     *
     * @param string $colorflag
     *
     * @return Flag
     */
    public function setColorflag($colorflag)
    {
        $this->colorflag = $colorflag;

        return $this;
    }

    /**
     * Get colorflag
     *
     * @return string
     */
    public function getColorflag()
    {
        return $this->colorflag;
    }

    /**
     * Set widthflag
     *
     * @param string $widthflag
     *
     * @return Flag
     */
    public function setWidthflag($widthflag)
    {
        $this->widthflag = $widthflag;

        return $this;
    }

    /**
     * Get widthflag
     *
     * @return string
     */
    public function getWidthflag()
    {
        return $this->widthflag;
    }

    /**
     * Set styleflag
     *
     * @param string $styleflag
     *
     * @return Flag
     */
    public function setStyleflag($styleflag)
    {
        $this->styleflag = $styleflag;

        return $this;
    }

    /**
     * Get styleflag
     *
     * @return string
     */
    public function getStyleflag()
    {
        return $this->styleflag;
    }
}

