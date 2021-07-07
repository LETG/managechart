<?php

namespace App\Chart\AvailableChoice;

/*
 * Classe qui liste les dash style possibles pour les series des graphiques
 */
class AvailableDashStyleSerie {
    public static $dashStyleSerie = array(
        'Solid' => 'listDashStyleSerie.solid',
        'ShortDash' => 'listDashStyleSerie.shortdash',
        'ShortDot' => 'listDashStyleSerie.shortdot',
        'ShortDashDot' => 'listDashStyleSerie.shortdashdot',
        'ShortDashDotDot' => 'listDashStyleSerie.shortdashdotdot',
        'Dot' => 'listDashStyleSerie.dot',
        'Dash' => 'listDashStyleSerie.dash',
        'LongDash' => 'listDashStyleSerie.longdash',
        'DashDot' => 'listDashStyleSerie.dashdot',
        'LongDashDot' => 'listDashStyleSerie.longdashdot',
        'LongDashDotDot' => 'listDashStyleSerie.longdashdotdot'
    );
}
