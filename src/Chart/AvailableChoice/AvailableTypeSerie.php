<?php

namespace App\Chart\AvailableChoice;

/*
 * Classe qui liste les types possibles pour les series des graphiques
 */
class AvailableTypeSerie
{
    public static $typesSerie = array(
        'line' => 'listTypeSerie.line',
        'spline' => 'listTypeSerie.spline',
        'scatter' => 'listTypeSerie.scatter',
        'area' => 'listTypeSerie.area',
        'areaspline' => 'listTypeSerie.areaspline',
        'column' => 'listTypeSerie.column',
        'bar' => 'listTypeSerie.bar',
        'pie' => 'listTypeSerie.pie',
        'errorbar' => 'listTypeSerie.errorbar',
        'heatmap' => 'listTypeSerie.heatmap'
    );

    public static $typesStack = array(
        'normal' => 'formChart.normal',
        'percent' => 'formChart.percent'
    );

    public static $sizetype = array(
        '0%' => '0%',
        '10%' => '10%',
        '20%' => '20%',
        '30%' => '30%',
        '40%' => '40%',
        '50%' => '50%',
        '60%' => '60%',
        '70%' => '70%',
        '80%' => '80%',
        '90%' => '90%',
        '100%' => '100%',
        '110%' => '110%',
        '120%' => '120%',
        '130%' => '130%',
        '140%' => '140%',
        '150%' => '150%',
    );
}
