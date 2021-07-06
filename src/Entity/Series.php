<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Series
 *
 * @ORM\Table(name="series")
 * @ORM\Entity(repositoryClass="App\Repository\SeriesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Series
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
     * @ORM\Column(name="titleSerie", type="string", length=255, nullable=true)
     */
    private $titleSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="unitSerie", type="string", length=255, nullable=true)
     */
    private $unitSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="typeSerie", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $typeSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="colorSerie", type="string", length=255, nullable=true)
     */
    private $colorSerie;

    /**
     * @var bool
     *
     * @ORM\Column(name="markerSerie", type="boolean")
     */
    private $markerSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="dashStyleSerie", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $dashStyleSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="parameterDataList", type="string", length=255, nullable=true)
     */
    private $parameterDataList;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\YAxis", inversedBy="series")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $yAxis;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataList", inversedBy="series")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $dataList;

    /**
     * @var int
     *
     * @ORM\Column(name="yaxisOrder", type="integer", nullable=true)
     */
    private $yaxisOrder;    


    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=255, nullable=true)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="innersize", type="string", length=255, nullable=true)
     */
    private $innersize;

    /**
     * @var float
     *
     * @ORM\Column(name="colsize", type="float", nullable=true, options={"default": 1.00})
     */
    private $colsize;


/*CONSTRUCTOR*/

 public function __construct() {
 
        $this->size = '100%';
        $this->innersize = '0%';
        $this->typeSerie = 'line';
        $this->colsize = 1.00;
    }

    /**
     * Set yaxisOrder
     *
     * @param integer $yaxisOrder
     * @return Series
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
        $this->yAxis->removeSeries($this);
    }

    /*
     * @ORM\PreRemove
     */
    public function updateDataList() {
        $this->dataList->removeSeries($this);
    }

    /**
     * Set yAxis
     *
     * @param \App\Entity\YAxis $yAxis
     * @return Series
     */
    public function setYAxis(\App\Entity\YAxis $yAxis) {
        $this->yAxis = $yAxis;
        $this->yAxis->addSeries($this);

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
     * Set dataList
     *
     * @param \App\Entity\DataList $dataList
     * @return Series
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titleSerie
     *
     * @param string $titleSerie
     * @return Series
     */
    public function setTitleSerie($titleSerie)
    {
        $this->titleSerie = $titleSerie;

        return $this;
    }

    /**
     * Get titleSerie
     *
     * @return string
     */
    public function getTitleSerie()
    {
        return $this->titleSerie;
    }

    /**
     * Set unitSerie
     *
     * @param string $unitSerie
     * @return Series
     */
    public function setUnitSerie($unitSerie)
    {
        $this->unitSerie = $unitSerie;

        return $this;
    }

    /**
     * Get unitSerie
     *
     * @return string
     */
    public function getUnitSerie()
    {
        return $this->unitSerie;
    }

    /**
     * Set typeSerie
     *
     * @param string $typeSerie
     * @return Series
     */
    public function setTypeSerie($typeSerie)
    {
        $this->typeSerie = $typeSerie;

        return $this;
    }

    /**
     * Get typeSerie
     *
     * @return string
     */
    public function getTypeSerie()
    {
        return $this->typeSerie;
    }

    /**
     * Set colorSerie
     *
     * @param string $colorSerie
     * @return Series
     */
    public function setColorSerie($colorSerie)
    {
        $this->colorSerie = $colorSerie;

        return $this;
    }

    /**
     * Get colorSerie
     *
     * @return string
     */
    public function getColorSerie()
    {
        return $this->colorSerie;
    }

    /**
     * Set markerSerie
     *
     * @param boolean $markerSerie
     * @return Series
     */
    public function setMarkerSerie($markerSerie)
    {
        $this->markerSerie = $markerSerie;

        return $this;
    }

    /**
     * Get markerSerie
     *
     * @return bool
     */
    public function getMarkerSerie()
    {
        return $this->markerSerie;
    }

    /**
     * Set dashStyleSerie
     *
     * @param string $dashStyleSerie
     * @return Series
     */
    public function setDashStyleSerie($dashStyleSerie)
    {
        $this->dashStyleSerie = $dashStyleSerie;

        return $this;
    }

    /**
     * Get dashStyleSerie
     *
     * @return string
     */
    public function getDashStyleSerie()
    {
        return $this->dashStyleSerie;
    }

    /**
     * Set parameterDataList
     *
     * @param string $parameterDataList
     * @return Series
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
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set size
     *
     * @param string $size
     * @return Series
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get innersize
     *
     * @return string
     */   
    public function getInnersize()
    {
        return $this->innersize;
    }
    
    /**
     * Set innersize
     *
     * @param string $size
     * @return Series
     */
    public function setInnersize($innersize)
    {
        $this->innersize = $innersize;
        return $this;
    }

    /**
     * Set Colsize
     *
     * @param double $colsize
     * @return YAxis
     */
    public function setColsize($colsize)
    {
        $this->colsize = $colsize;

        return $this;
    }

    /**
     * Get Colsize
     *
     * @return double
     */
    public function getColsize()
    {
        return $this->colsize;
    }  
}

