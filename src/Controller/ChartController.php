<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Chart;
use App\Entity\DataList;
use App\Entity\YAxis;
use App\Form\ChartType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Chart\AvailableChoice\AvailableColorSerie;
use App\Chart\AvailableChoice\AvailableTypeSerie;
use App\Chart\AvailableChoice\AvailableTypeAxis;
use App\Chart\AvailableChoice\AvailableDashStyleSerie;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\Enums\Action;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Security\Core\Security;

use App\Entity\User;


class ChartController extends AbstractController
{
    private static $varTwig;

    private $translator;
    
    /**
     * @author Philippe Grison  <philippe.grison@mnhn.fr>
     */
    private $doctrine;

    public function __construct(TranslatorInterface $translator, ManagerRegistry $doctrine, Security $security)
    {
        $this->translator = $translator;
        $this->doctrine = $doctrine;
        $this->security = $security;
    }

    protected function initializeVarTwig($manager)
    {
        $translator = $this->translator;

        $jsTranslate = array(
            'formYAxis_logarithmicError_name' => $translator->trans('formYAxis.logarithmicError.name'),
            'formYAxis_logarithmicError_description' => $translator->trans('formYAxis.logarithmicError.description')
        );

        self::$varTwig = array(
            'list_yAxis'         => null,
            'form'               => null,
            'listDataList'       => self::getListDataList($manager),
            'listTypeAxis'       => AvailableTypeAxis::$typesAxis,
            'listTypeSerie'      => AvailableTypeSerie::$typesSerie,
            'listColorSerie'     => AvailableColorSerie::$colorSerie,
            'listDashStyleSerie' => AvailableDashStyleSerie::$dashStyleSerie,
            'firstTypeAxis'      => key(AvailableTypeAxis::$typesAxis),
            'firstTypeSerie'     => key(AvailableTypeSerie::$typesSerie),
            'firstColorSerie'    => key(AvailableColorSerie::$colorSerie),
            'jsTranslate'        => $jsTranslate
        );
        return self::$varTwig;
    }


    /**
     * Retourne la liste des dataList en BDD
     */
    protected static function getListDataList($manager)
    {
        $repository = $manager->getRepository(DataList::class);
        return $repository->findBy([], ["nameData" => "ASC"]);
    }

    /**
     * @Route("/")
     */
    public function index()
    {
        $repository = $this->doctrine->getRepository(Chart::class);
        $list_chart = $repository->findAll();
        $repositoryUser = $this->doctrine->getRepository(User::class);
        $user_admin = $repositoryUser->findBy(array('id' => $this->security->getUser()->getUserCre()),array('userCre' => 'ASC'),1 ,0)[0];

        return $this->render('chart/index.html.twig', array('list_chart' => $list_chart, 'user_admin' => $user_admin));
    }

    /**
     * Prépare le formulaire de création d'un graphique
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function create(Request $request, $typeChart)
    {
        $chart = new Chart();
        $chart->setTypeChart($typeChart);

        /* Si graphique temporel */
        if ($typeChart == 'timechart' || $typeChart == 'multiaxistimechart' || $typeChart == 'timedynamicchart') {
            $chart->setXAxisType('datetime');
        }

        $form = $this->createForm(ChartType::class, $chart, ['action_type' => Action::create->value,]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->register($chart, false);
                return $chart;
            }
        }

        /* Initialisation des varibles twig */
        $em = $this->doctrine->getManager();
        $varTwig = self::initializeVarTwig($em);
        $varTwig['form'] = $form->createView();

        return $varTwig;
    }

    /**
     * Prépare le formulaire d'édition d'un graphique
     * @isGranted("ROLE_SCIENTIFIC")
     */
    protected function edit(Request $request, $chart)
    {
        $em = $this->doctrine->getManager();
        $user = $this->getUser();
        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        $listYAxis = $chart->getListYAxis()->toArray();

            // On supprime les axes Y et leurs séries/flags
        foreach ($chart->getListYAxis() as $yAxis) {
            foreach ($yAxis->getSeries() as $series) {
            $em->remove($series);
            }
            foreach ($yAxis->getFlag() as $flag) {
            $em->remove($flag);
            }
            $em->remove($yAxis);
        }

        $chart->getListYAxis()->clear();

        // On pré-remplie le formulaire avec les données du graphique + ses axes + ses séries/flags
        $form = $this->createForm(ChartType::class, $chart, ['action_type' => Action::edit->value,]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->register($chart, true);
                return $chart;
            }
        }

            /* Initialisation des varibles twig */
        $varTwig = self::initializeVarTwig($em);
        $varTwig['list_yAxis'] = $listYAxis;
        $varTwig['form'] = $form->createView();

        return $varTwig;
    }

    /**
     * Enregistrement du chart en BDD
     */
    protected function register($chart, $edit)
    {
        $em = $this->doctrine->getManager();

            // On enregistre le graphique
            if (!$edit) {
            $em->persist($chart);
        }

        // On enregistre chaque axe Y avec ses séries et/ou flags
        foreach ($chart->getListYAxis() as $yAxis) {
            $yAxis->setChart($chart);
            $em->persist($yAxis);

            foreach ($yAxis->getSeries() as $serie) {
                $serie->setYAxis($yAxis);
                $em->persist($serie);
            }

            foreach ($yAxis->getFlag() as $flag) {
                $flag->setYAxis($yAxis);
                $em->persist($flag);
            }
        }

        $em->flush();
    }

    /**
     * Retourne la dataList au format JSON
     */
    public function getDataParameterDataList(DataList $dataList, $attributsSpatiaux, $editCreate)
    {
        if (!$attributsSpatiaux) {
            $dataList->executeQuery("default", false, false);
        } else {
            $dataList->executeQuery($attributsSpatiaux, false, false);
        }
        $listData = $dataList->getListDataByParameter();

        $dataJSON = "{";
        $regex = "#[^0-9.-]#";

        /** Les valeurs de x et y peuvent être des chaines de caractères ou numérique, il y a 4 différents cas :
         *  x et y => string, x et y => numeric, x => string et y => numeric, x => numeric et y => string
         */
        foreach ($listData as $key => $value) {
            if (!empty($value)) {
            $dataJSON .= "" . json_encode($key) . " : [";

            /* x => string */
            if (preg_match($regex, $value[0][0])) {
                /* y => string */
                if (preg_match($regex, $value[0][1])) {
                foreach ($value as $item) {
                    $dataJSON .= (isset($item[1]))  ? '[' . json_encode($item[0]) . ',' . json_encode($item[1]) . '],'
                                : '[' . json_encode($item[0]) . ',null],';
                }
                }
                /* y => numeric */
                else {
                foreach ($value as $item) {
                    $dataJSON .= (isset($item[1]))  ? '[' . json_encode($item[0]) . ',' . $item[1] . '],'
                                : '[' . json_encode($item[0]) . ',null],';
                }
                }
            }
            /* x => numeric */
            else {
                /* y => string */
                if (preg_match($regex, $value[0][1])) {
                foreach ($value as $item) {
                    $dataJSON .= (isset($item[1]))  ? '[' . $item[0] . ',' . json_encode($item[1]) . '],'
                                : '[' . $item[0] . ',null],';
                }
                }
                /* y => numeric */
                else {
                foreach ($value as $item) {
                    $dataJSON .= (isset($item[1]))  ? '[' . $item[0] . ',' . $item[1] . '],'
                                : '[' . $item[0] . ',null],';
                }
                }
            }
            }
            /* Suppression de la dernière virgule */
            $dataJSON = substr($dataJSON, 0, strlen($dataJSON) -1);
            $dataJSON .= "],";
        }
        $dataJSON = substr($dataJSON, 0, strlen($dataJSON) -1);
        $dataJSON .= "}";

        $response = new Response();
        $response->setContent($dataJSON);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function confirmed(Chart $chart)
    {
        return $this->render('chart/confirmed.html.twig', array('chart' => $chart));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function duplicate(Chart $chart)
    {
        $em = $this->doctrine->getManager();
        $user = $this->getUser();

        // Duplication du graphique
        $duplicatedChart = clone $chart;
        $duplicatedChart->setNameChart('Copie de ' . $chart->getNameChart());
        $duplicatedChart->setUserCre($user->getId());
        $duplicatedChart->setDateCre(new \DateTime());
        $duplicatedChart->setUserMaj($user->getId());
        $duplicatedChart->setDateMaj(new \DateTime());

        $em->persist($duplicatedChart);

        // Duplication de chaque axe Y avec ses séries et/ou flags
        foreach ($chart->getListYAxis() as $yAxis) {

            $duplicatedYAxis = clone $yAxis;
            $duplicatedYAxis->setChart($duplicatedChart);
            $em->persist($duplicatedYAxis);

            foreach ($yAxis->getSeries() as $serie) {
                $duplicatedSerie = clone $serie;
                $duplicatedSerie->setYAxis($duplicatedYAxis);
                $em->persist($duplicatedSerie);
            }

            foreach ($yAxis->getFlag() as $flag) {
                $duplicatedFlag = clone $flag;
                $duplicatedFlag->setYAxis($duplicatedYAxis);
                $em->persist($duplicatedFlag);
            }
        }

        $em->flush();

        return $this->redirectToRoute('index');
    }

    public function show(Chart $chart)
    {
        return $this->render('chart/show.html.twig', array('chart' => $chart));
    }

    public function showIframe(Request $request, Chart $chart, $width, $height, $attributsSpatiaux, $test)
    {
        return $this->render('chart/showIframe.html.twig', array(
            'chart' => $chart,
            'listColorSerie' => AvailableColorSerie::$colorSerie,
            'width' => $width === "auto" ? "null" : $width,
            'height' => $height === "auto" ? "null" : $height,
            'attributsSpatiaux' => $attributsSpatiaux,
            'test' => $test, // Le paramètre test sert à définir si on utilise les attributs spatiaux présents dans l'URL ou ceux présent en BDD (true : BDD / false : URL)
            'background' =>$request->get('background'),
            'axis' =>$request->get('axis'),
            'label' =>$request->get('label'),
            'fontSize' =>$request->get('fontSize')
        ));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function delete(Request $request, Chart $chart)
    {
        $em = $this->doctrine->getManager();
        $user = $this->getUser();

        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        
        $em->remove($chart);
        $em->flush();

        return $this->redirect($this->generateUrl('index2', array('_locale' =>$request->getLocale())));
    }

    /* * * * * * * GRAPHIQUE SIMPLE * * * * * * */

    /**
     * Création d'un graphique simple
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function newSimplechart(Request $request)
    {
        $returnCreate = $this->create($request, 'simplechart');

        if ($returnCreate instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnCreate->getId())));
        } else {
            return $this->render('chart/simplechart/new.html.twig', $returnCreate);
        }
    }

    /**
     * Modification d'un graphique simple
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function editSimplechart(Request $request, Chart $chart)
    {

        $returnEdit = $this->edit($request, $chart);
        $user = $this->getUser();
        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        if ($returnEdit instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnEdit->getId())));
        } else {
            return $this->render('chart/simplechart/edit.html.twig', $returnEdit);
        }
    }

    /* * * * * * * GRAPHIQUE TEMPOREL * * * * * * */

    /**
     * Création d'un graphique temporel
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function newTimechart(Request $request)
    {
        $returnCreate = $this->create($request, 'timechart');

        if ($returnCreate instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnCreate->getId())));
        } else {
            return $this->render('chart/timechart/new.html.twig', $returnCreate);
        }
    }

    /**
     * Modification d'un graphique temporel
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function editTimechart(Request $request, Chart $chart)
    {
        $returnEdit = $this->edit($request, $chart);
        $user = $this->getUser();
        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        if ($returnEdit instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnEdit->getId())));
        } else {
            return $this->render('chart/timechart/edit.html.twig', $returnEdit);
        }
    }

    /* * * * * * * GRAPHIQUE TEMPOREL MULTI-AXES * * * * * * */

    /**
     * Création d'un graphique temporel multi-axes
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function newMultiaxistimechart(Request $request)
    {
        $returnCreate = $this->create($request, 'multiaxistimechart');

        if ($returnCreate instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnCreate->getId())));
        } else {
            return $this->render('chart/multiaxistimechart/new.html.twig', $returnCreate);
        }
    }

    /**
     * Modification d'un graphique temporel multi-axes
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function editMultiaxistimechart(Request $request, Chart $chart)
    {
        $returnEdit = $this->edit($request, $chart);
        $user = $this->getUser();
        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        if ($returnEdit instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnEdit->getId())));
        } else {
            return $this->render('chart/multiaxistimechart/edit.html.twig', $returnEdit);
        }
    }

    /* * * * * * * GRAPHIQUE CIRCULAIRE * * * * * * */

    /**
     * Création d'un graphique circulaire
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function newPiechart(Request $request)
    {
        $returnCreate = $this->create($request, 'piechart');

        if ($returnCreate instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnCreate->getId())));
        } else {
            return $this->render('chart/piechart/new.html.twig', $returnCreate);
        }
    }

    /**
     * Modification d'un graphique circulaire
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function editPiechart(Request $request, Chart $chart)
    {
        $returnEdit = $this->edit($request, $chart);
        $user = $this->getUser();
        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        if ($returnEdit instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnEdit->getId())));
        } else {
            return $this->render('chart/piechart/edit.html.twig', $returnEdit);
        }
    }

    /* * * * * * * GRAPHIQUE DYNAMIQUE * * * * * * */

    /**
     * Création d'un graphique dynamique
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function newDynamicchart(Request $request)
    {
        $returnCreate = $this->create($request, 'dynamicchart');

        if ($returnCreate instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnCreate->getId())));
        } else {
            return $this->render('chart/dynamicchart/new.html.twig', $returnCreate);
        }
    }

    /**
     * Modification d'un graphique dynamique
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function editDynamicchart(Request $request, Chart $chart)
    {
        $returnEdit = $this->edit($request, $chart);
        $user = $this->getUser();
        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        if ($returnEdit instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnEdit->getId())));
        } else {
            return $this->render('chart/dynamicchart/edit.html.twig', $returnEdit);
        }
    }

    /* * * * * * * GRAPHIQUE POLAIRE * * * * * * */

    /**
     * Création d'un graphique polar et spiderweb
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function newPolarchart(Request $request)
    {
        $returnCreate = $this->create($request, 'polarchart');

        if ($returnCreate instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnCreate->getId())));
        } else {
            return $this->render('chart/polarchart/new.html.twig', $returnCreate);
        }
    }

    /**
     * Modification d'un graphique polar et spiderweb
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function editPolarchart(Request $request, Chart $chart)
    {
        $returnEdit = $this->edit($request, $chart);
        $user = $this->getUser();
        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        if ($returnEdit instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnEdit->getId())));
        } else {
            return $this->render('chart/polarchart/edit.html.twig', $returnEdit);
        }
    }

    /* * * * * * * GRAPHIQUE HEATMAP * * * * * * */

    /**
     * Création d'un graphique heatmap
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function newHeatmapchart(Request $request)
    {
        $returnCreate = $this->create($request, 'heatmapchart');

        if ($returnCreate instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnCreate->getId())));
        } else {
            return $this->render('chart/heatmapchart/new.html.twig', $returnCreate);
        }
    }

    /**
     * Modification d'un graphique heatmap
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function editHeatmapchart(Request $request, Chart $chart)
    {
        $returnEdit = $this->edit($request, $chart);
        $user = $this->getUser();
        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        if ($returnEdit instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnEdit->getId())));
        } else {
            return $this->render('chart/heatmapchart/edit.html.twig', $returnEdit);
        }
    }

    /* * * * * * * GRAPHIQUE DYNAMIQUE TEMPOREL * * * * * * */

    /**
     * Création d'un graphique dynamique temporel
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function newTimedynamicchart(Request $request)
    {
        $returnCreate = $this->create($request, 'timedynamicchart');

        if ($returnCreate instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnCreate->getId())));
        } else {
            return $this->render('chart/timedynamicchart/new.html.twig', $returnCreate);
        }
    }

    /**
     * Modification d'un graphique dynamique temporel
     * @isGranted("ROLE_SCIENTIFIC")
     */
    public function editTimedynamicchart(Request $request, Chart $chart)
    {
        $returnEdit = $this->edit($request, $chart);
        $user = $this->getUser();
        if ($user->getRoles()[0] != 'ROLE_ADMIN' && $chart->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }

        if ($returnEdit instanceof Chart) {
            return $this->redirect($this->generateUrl('chart_registration_confirmed', array('chart' => $returnEdit->getId())));
        } else {
            return $this->render('chart/timedynamicchart/edit.html.twig', $returnEdit);
        }
    }
}
