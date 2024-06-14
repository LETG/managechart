<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Chart
 *
 * @ORM\Table(name="chart")
 * @ORM\Entity(repositoryClass="App\Repository\ChartRepository")
 */
class Chart
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
     * @ORM\Column(name="nameChart", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nameChart;

    /**
     * @var string
     *
     * @ORM\Column(name="titleChart", type="string", length=255, nullable=true)
     */
    private $titleChart;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitleChart", type="string", length=255, nullable=true)
     */
    private $subtitleChart;

    /**
     * @var bool
     *
     * @ORM\Column(name="legendChart", type="boolean")
     */
    private $legendChart;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="tooltipChart", type="boolean", options={"default" : true})
     */
    private $tooltipChart;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="publicChart", type="boolean", options={"default" : true})
     */
    private $publicChart = 1 ;
    

    /**
     * @var string
     *
     * @ORM\Column(name="creditsChart", type="text")
     * @Assert\NotBlank()
     */
    private $creditsChart;

    /**
     * @var string
     *
     * @ORM\Column(name="urlcreditsChart", type="text", nullable=true)
     */
    private $urlcreditsChart;

    /**
     * @var string
     *
     * @ORM\Column(name="typeChart", type="text")
     * @Assert\NotBlank()
     */
    private $typeChart;

    /**
     * @var string
     *
     * @ORM\Column(name="xAxisTitle", type="string", length=255, nullable=true)
     */
    private $xAxisTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="xAxisUnit", type="string", length=255, nullable=true)
     */
    private $xAxisUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="xAxisType", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $xAxisType;

    /**
     * @var bool
     *
     * @ORM\Column(name="invertedChart", type="boolean", nullable=true)
     */
    private $invertedChart;

    /**
     * @var int
     *
     * @ORM\Column(name="gapSizeChart", type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     */
    private $gapSizeChart;

    /**
     * @var string
     *
     * @ORM\Column(name="polarType", type="string", length=255, nullable=true)
     */
    private $polarType;



     /**
     * @var string
     *
     * @ORM\Column(name="pieType", type="string", length=255, nullable=true)
     */


    private $pieType;

    /**
     * @var string
     *
     * @ORM\Column(name="typestacked", type="string", length=255 , nullable=true)
     */

    private $typestacked;



    /**
     * @var bool
     *
     * @ORM\Column(name="exportPrintChart", type="boolean")
     */
    private $exportPrintChart;

    /**
     * @var bool
     *
     * @ORM\Column(name="exportCSVChart", type="boolean")
     */
    private $exportCSVChart;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\YAxis", mappedBy="chart", cascade={"persist", "remove"})
     */
    private $list_yAxis;  

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_cre", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateCre;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_maj", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateMaj;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_cre", type="integer", nullable=true, options={"default"="1"})
     */
    private $userCre = '1';

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_maj", type="integer", nullable=true, options={"default"="1"})
     */
    private $userMaj = '1';


    public function __construct() {
        $this->list_yAxis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->legendChart = true;
        $this->tooltipChart = true;
        $this->gapSizeChart = 0;
        $this->exportPrintChart = false;
        $this->exportCSVChart = false;
        $this->typestacked = '';
    }
   
    /**
     * Add list_yAxis
     *
     * @param \App\Entity\YAxis $listYAxis
     * @return Chart
     */
    public function addListYAxi(\App\Entity\YAxis $listYAxis) {
        $this->list_yAxis[] = $listYAxis;
        return $this;
    }


/**  GETTERS SETTERS TYPE OF STACKED  NORMAL OR PERCENT **/
    public function gettypestacked(){
        return $this->typestacked;
    }

    public function settypestacked($typestacked){
        $this->typestacked = $typestacked;
        return $this;

    }

    // /**
    //  * Remove list_yAxis
    //  *
    //  * @param \App\Entity\YAxis $listYAxis
    //  */
    // public function removeListYAxi(\App\Entity\YAxis $listYAxis) {
    //     $this->list_yAxis->removeElement($listYAxis);
    // }

    /**
     * Get list_yAxis
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getListYAxis() {
        return $this->list_yAxis;
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
     * Set nameChart
     *
     * @param string $nameChart
     * @return Chart
     */
    public function setNameChart($nameChart)
    {
        $this->nameChart = $nameChart;

        return $this;
    }

    /**
     * Get nameChart
     *
     * @return string
     */
    public function getNameChart()
    {
        return $this->nameChart;
    }

    /**
     * Set titleChart
     *
     * @param string $titleChart
     * @return Chart
     */
    public function setTitleChart($titleChart)
    {
        $this->titleChart = $titleChart;

        return $this;
    }

    /**
     * Get titleChart
     *
     * @return string
     */
    public function getTitleChart()
    {
        return $this->titleChart;
    }

    /**
     * Set subtitleChart
     *
     * @param string $subtitleChart
     * @return Chart
     */
    public function setSubtitleChart($subtitleChart)
    {
        $this->subtitleChart = $subtitleChart;

        return $this;
    }

    /**
     * Get subtitleChart
     *
     * @return string
     */
    public function getSubtitleChart()
    {
        return $this->subtitleChart;
    }

    /**
     * Set legendChart
     *
     * @param boolean $legendChart
     * @return Chart
     */
    public function setLegendChart($legendChart)
    {
        $this->legendChart = $legendChart;

        return $this;
    }

    /**
     * Get legendChart
     *
     * @return bool
     */
    public function getLegendChart()
    {
        return $this->legendChart;
    }

    /**
     * Set tooltipChart
     *
     * @param boolean $tooltipChart
     * @return Chart
     */
    public function setTooltipChart($tooltipChart)
    {
        $this->tooltipChart = $tooltipChart;

        return $this;
    }

    /**
     * Get tooltipChart
     *
     * @return bool
     */
    public function getTooltipChart()
    {
        return $this->tooltipChart;
    }

    /**
     * Set creditsChart
     *
     * @param string $creditsChart
     * @return Chart
     */
    public function setCreditsChart($creditsChart)
    {
        $this->creditsChart = $creditsChart;

        return $this;
    }

    /**
     * Get creditsChart
     *
     * @return string
     */
    public function getCreditsChart()
    {
        return $this->creditsChart;
    }

    /**
     * Set urlcreditsChart
     *
     * @param string $urlcreditsChart
     * @return Chart
     */
    public function setUrlcreditsChart($urlcreditsChart)
    {
        $this->urlcreditsChart = $urlcreditsChart;

        return $this;
    }

    /**
     * Get urlcreditsChart
     *
     * @return string
     */
    public function getUrlcreditsChart()
    {
        return $this->urlcreditsChart;
    }

    /**
     * Set typeChart
     *
     * @param string $typeChart
     * @return Chart
     */
    public function setTypeChart($typeChart)
    {
        $this->typeChart = $typeChart;

        return $this;
    }

    /**
     * Get typeChart
     *
     * @return string
     */
    public function getTypeChart()
    {
        return $this->typeChart;
    }

    /**
     * Set xAxisTitle
     *
     * @param string $xAxisTitle
     * @return Chart
     */
    public function setXAxisTitle($xAxisTitle)
    {
        $this->xAxisTitle = $xAxisTitle;

        return $this;
    }

    /**
     * Get xAxisTitle
     *
     * @return string
     */
    public function getXAxisTitle()
    {
        return $this->xAxisTitle;
    }

    /**
     * Set xAxisUnit
     *
     * @param string $xAxisUnit
     * @return Chart
     */
    public function setXAxisUnit($xAxisUnit)
    {
        $this->xAxisUnit = $xAxisUnit;

        return $this;
    }

    /**
     * Get xAxisUnit
     *
     * @return string
     */
    public function getXAxisUnit()
    {
        return $this->xAxisUnit;
    }

    /**
     * Set xAxisType
     *
     * @param string $xAxisType
     * @return Chart
     */
    public function setXAxisType($xAxisType)
    {
        $this->xAxisType = $xAxisType;

        return $this;
    }

    /**
     * Get xAxisType
     *
     * @return string
     */
    public function getXAxisType()
    {
        return $this->xAxisType;
    }

    /**
     * Set invertedChart
     *
     * @param boolean $invertedChart
     * @return Chart
     */
    public function setInvertedChart($invertedChart)
    {
        $this->invertedChart = $invertedChart;

        return $this;
    }

    /**
     * Get invertedChart
     *
     * @return bool
     */
    public function getInvertedChart()
    {
        return $this->invertedChart;
    }

    /**
     * Set gapSizeChart
     *
     * @param integer $gapSizeChart
     * @return Chart
     */
    public function setGapSizeChart($gapSizeChart)
    {
        $this->gapSizeChart = $gapSizeChart;

        return $this;
    }

    /**
     * Get gapSizeChart
     *
     * @return int
     */
    public function getGapSizeChart()
    {
        return $this->gapSizeChart;
    }

    /**
     * Set exportPrintChart
     *
     * @param boolean $exportPrintChart
     * @return Chart
     */
    public function setExportPrintChart($exportPrintChart)
    {
        $this->exportPrintChart = $exportPrintChart;

        return $this;
    }

    /**
     * Get exportPrintChart
     *
     * @return bool
     */
    public function getExportPrintChart()
    {
        return $this->exportPrintChart;
    }

    /**
     * Set exportCSVChart
     *
     * @param boolean $exportCSVChart
     * @return Chart
     */
    public function setExportCSVChart($exportCSVChart)
    {
        $this->exportCSVChart = $exportCSVChart;

        return $this;
    }

    /**
     * Get exportCSVChart
     *
     * @return bool
     */
    public function getExportCSVChart()
    {
        return $this->exportCSVChart;
    }

    /**
     * Set polarType
     *
     * @param string $polarType
     *
     * @return Chart
     */
    public function setPolarType($polarType)
    {
        $this->polarType = $polarType;

        return $this;
    }

    /**
     * Get polarType
     *
     * @return string
     */
    public function getPolarType()
    {
        return $this->polarType;
    }


        /**
     * Set pieType
     *
     * @param string $pieType
     *
     * @return Chart
     */
    public function setPieType($pieType)
    {
        $this->pieType = $pieType;

        return $this; 

    }

     /**
     * Get pieType
     *
     * @return string
     */
    public function getPieType(){

        return $this->pieType;

    }

    public function isLegendChart(): ?bool
    {
        return $this->legendChart;
    }

    public function isTooltipChart(): ?bool
    {
        return $this->tooltipChart;
    }

    public function isInvertedChart(): ?bool
    {
        return $this->invertedChart;
    }

    public function isExportPrintChart(): ?bool
    {
        return $this->exportPrintChart;
    }

    public function isExportCSVChart(): ?bool
    {
        return $this->exportCSVChart;
    }

    public function getDateCre(): ?\DateTimeInterface
    {
        return $this->dateCre;
    }

    public function setDateCre(?\DateTimeInterface $dateCre): static
    {
        $this->dateCre = $dateCre;

        return $this;
    }

    public function getDateMaj(): ?\DateTimeInterface
    {
        return $this->dateMaj;
    }

    public function setDateMaj(?\DateTimeInterface $dateMaj): static
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }
    
    public function removeListYAxi(YAxis $listYAxi): static
    {
        if ($this->list_yAxis->removeElement($listYAxi)) {
            // set the owning side to null (unless already changed)
            if ($listYAxi->getChart() === $this) {
                $listYAxi->setChart(null);
            }
        }

        return $this;
    }

    public function isPublicChart(): ?bool
    {
        return $this->publicChart;
    }

    public function setPublicChart(bool $publicChart): static
    {
        $this->publicChart = $publicChart;

        return $this;
    }

    public function getUserCre(): ?int
    {
        return $this->userCre;
    }

    public function setUserCre(?int $userCre): static
    {
        $this->userCre = $userCre;

        return $this;
    }

    public function getUserMaj(): ?int
    {
        return $this->userMaj;
    }

    public function setUserMaj(?int $userMaj): static
    {
        $this->userMaj = $userMaj;

        return $this;
    }
}
