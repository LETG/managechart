<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * YAxis
 *
 * @ORM\Table(name="y_axis")
 * @ORM\Entity(repositoryClass="App\Repository\YAxisRepository")
 * @ORM\HasLifecycleCallbacks
 */
class YAxis
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
     * @ORM\Column(name="titleYAxis", type="string", length=255, nullable=true)
     */
    private $titleYAxis;

    /**
     * @var string
     *
     * @ORM\Column(name="typeYAxis", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $typeYAxis;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Flag", mappedBy="yAxis", cascade={"persist", "remove"})
     */
    private $flag;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Series", mappedBy="yAxis", cascade={"persist", "remove"})
     */
    private $series;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Chart", inversedBy="list_yAxis")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $chart;

    /**
     * @var string
     *
     * @ORM\Column(name="top", type="string", length=255, nullable=true)
     */
    private $top;

    /**
     * @var string
     *
     * @ORM\Column(name="height", type="string", length=255, nullable=true)
     */
    private $height;

    /**
     * @var boolean
     *
     * @ORM\Column(name="opposite", type="boolean", options={"default": false})
     */
    private $opposite;
    
    /**
     * @var int
     *
     * @ORM\Column(name="orderY", type="integer", nullable=true)
     */
    private $orderY; 



    public function __construct() {
        $this->series = new \Doctrine\Common\Collections\ArrayCollection();
        $this->flag = new \Doctrine\Common\Collections\ArrayCollection();
        $this->typeYAxis = 'linear';
        $this->top = '5%';
        $this->height = '100%';
    }

    public function gettop(){
            return $this->top;
    }
    public function settop($top){
        $this->top = $top;
        return $this;
    }
    public function getheight(){
            return $this->height;
    }
    public function setheight($height){
        $this->height = $height;
        return $this;
    }

    public function getopposite(){
            return $this->opposite;
    }
    public function setopposite($opposite){
        $this->opposite = $opposite;
        return $this;
    }

    // /**
    //  * @ORM\PreRemove
    //  */
    // public function updateChart() {
    //     $this->chart->removeListYAxi($this);
    // }

    /**
     * Set chart
     *
     * @param \App\Entity\Chart $chart
     * @return YAxis
     */
    public function setChart(\App\Entity\Chart $chart) {
        $this->chart = $chart;
        $this->chart->addListYAxi($this);
        return $this;
    }

    /**
     * Get chart
     *
     * @return \App\Entity\Chart 
     */
    public function getChart() {
        return $this->chart;
    }

    /**
     * Add flag
     *
     * @param \App\Flag $flag
     * @return YAxis
     */
    public function addFlag(\App\Entity\Flag $flag) {
        $this->flag[] = $flag;

        return $this;
    }

    /**
     * Add series
     *
     * @param \App\Entity\Series $series
     * @return YAxis
     */
    public function addSeries(\App\Entity\Series $series) {
        $this->series[] = $series;

        return $this;
    }

    /**
     * Remove flag
     *
     * @param \App\Entity\Flag $flag
     */
    public function removeFlag(\App\Entity\Flag $flag) {
        $this->flag->removeElement($flag);
    }

    /**
     * Remove series
     *
     * @param \App\Entity\Series $series
     */
    public function removeSeries(\App\Entity\Series $series) {
        $this->series->removeElement($series);
    }

    /**
     * Get Flag
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlag() {
        return $this->flag;
    }

    /**
     * Get Series
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSeries() {
        return $this->series;
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
     * Set titleYAxis
     *
     * @param string $titleYAxis
     * @return YAxis
     */
    public function setTitleYAxis($titleYAxis)
    {
        $this->titleYAxis = $titleYAxis;

        return $this;
    }

    /**
     * Get titleYAxis
     *
     * @return string
     */
    public function getTitleYAxis()
    {
        return $this->titleYAxis;
    }

    /**
     * Set typeYAxis
     *
     * @param string $typeYAxis
     * @return YAxis
     */
    public function setTypeYAxis($typeYAxis)
    {
        $this->typeYAxis = $typeYAxis;

        return $this;
    }

    /**
     * Get typeYAxis
     *
     * @return string
     */
    public function getTypeYAxis()
    {
        return $this->typeYAxis;
    }
    
    /**
     * Set orderY
     *
     * @param integer $orderY
     * @return YAxis
     */
    public function setOrderY($orderY)
    {
        $this->orderY = $orderY;

        return $this;
    }

    /**
     * Get orderY
     *
     * @return int
     */
    public function getOrderY()
    {
        return $this->orderY;
    }

    public function isOpposite(): ?bool
    {
        return $this->opposite;
    }


}

