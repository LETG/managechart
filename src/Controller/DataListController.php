<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Bdd\Controller\Bdd;
use App\Entity\DataList;
use App\Entity\DataSource;
use App\Form\DataListType;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\Enums\Action;

class DataListController extends AbstractController
{
    /**
     * @author Philippe Grison  <philippe.grison@mnhn.fr>
     */
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }
       
    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function index() {
        $repository = $this->doctrine->getRepository(DataList::class);
        $list_dataList = $repository->findAll();

        return $this->render('data_list/index.html.twig', array('list_dataList' =>$list_dataList));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function new(Request $request, TranslatorInterface $translator) {
        $dataList = new DataList();
        $form = $this->createForm(DataListType::class, $dataList, ['action_type' => Action::create->value,]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /* Enregistrement de la dataList seul -> Generation d'un id
                   Enregistrement des attributs spatiaux correspondant */
                $attributsSpatiaux = $dataList->getAttributsSpatiaux()->toArray();
                $dataList->getAttributsSpatiaux()->clear();

                $em = $this->doctrine->getManager();
                $em->persist($dataList);
                $em->flush();

                foreach ($attributsSpatiaux as $attributSpatial) {
                    $attributSpatial->setDataList($dataList);

                    if ($attributSpatial->getKeywordAttribut() == null) {
                        $attributSpatial->setKeywordAttribut(' ');
                    }
                    $em->persist($attributSpatial);
                }
                $em->flush();

                return $this->redirect($this->generateUrl('data_list_registration_confirmed', array('id' =>$dataList->getId())));
            }
        }

        $jsTranslate = array(
            'formAttributes_add'    => $translator->trans('formAttributes.add'),
            'formAttributes_delete' => $translator->trans('formAttributes.delete'),
            'formAttributes_error'  => $translator->trans('formAttributes.error'),
            'spatialAttribute'      => $translator->trans('spatialAttribute')
        );

        return $this->render('data_list/form.html.twig', array(
            'form'                  => $form->createView(),
            'listMsgGetTimestamp'   => Bdd::getMsgTimestamp(),
            'title'                 => $translator->trans('datalist.new'),
            'edit'                  => false,
            'jsTranslate'           => $jsTranslate
        ));
    }

    /**
     * Retourne un DataList pour test
     */
    protected function getDataList($idDatasource, $request, $attributsSpatiaux, $test) {
        $repository = $this->doctrine->getRepository(DataSource::class);

        $dataSource = $repository->find($idDatasource);

        $dataList = new DataList();
        $dataList->setDataSource($dataSource);
        $dataList->setRequestData($request);

        try {
            if ($attributsSpatiaux != null) {
                echo $dataList->executeQuery($attributsSpatiaux, true, $test);
            } else {
                echo $dataList->executeQuery('default', true, $test);
            }
        } catch (Exception $e) {
            throw $e;
        }

        $listData = $dataList->getData();
        return $dataList;
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function queryBrut(TranslatorInterface $translator) {
        $idDatasource = $_POST['dataSource'];
        $request = $_POST['request'];
        $attributsSpatiaux = $_POST['attributsSpatiaux'];
        $test = $_POST['test'];

        if ($test === "true") {
            try {
                $dataList = $this->getDataList($idDatasource, $request, $attributsSpatiaux, true);
            } catch (Exception $e) {
                return new Response($e);
            }
        } else {
            try {
                $dataList = $this->getDataList($idDatasource, $request, $attributsSpatiaux, false);
            } catch (Exception $e) {
                return new Response($e);
            }
        }

        $fields = $dataList->getFields();
        $listData = $dataList->getData();
        $button = $translator->trans('request.button');

        $html = '<div style="margin-bottom:10px"><button class="btn btn-default" id="btncsvExp" type="button">' . $button . '</button></div>';
        $html .= '<table class="table table-striped"><thead><tr>';

        /* thead */
        foreach ($fields as $field) {
            $html .= '<th>' . $field . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        /* tbody */
        foreach ($listData as $data) {
            $html .= '<tr>';
            foreach ($data as $item) {
                $html .= '<td>' . $item . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return new Response($html);
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function confirmed(DataList $dataList) {
        return $this->render('data_list/confirmed.html.twig', array('dataList' => $dataList));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function delete(DataList $dataList) {
        $em = $this->doctrine->getManager();
        $em->remove($dataList);
        $em->flush();

        return $this->redirect($this->generateUrl('data_list_list'));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function edit(Request $request, TranslatorInterface $translator,  DataList $dataList) {
        $attributsSpatiaux = $dataList->getAttributsSpatiaux()->toArray();//array_reverse($dataList->getAttributsSpatiaux()->toArray());

        $em = $this->doctrine->getManager();
        foreach ($dataList->getAttributsSpatiaux()->toArray() as $attributSpatial) {
            $em->remove($attributSpatial);
        }
        $dataList->getAttributsSpatiaux()->clear();

        $form = $this->createForm(DataListType::class, $dataList, ['action_type' => Action::edit->value,]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /* Enregistrement de la dataList seul -> Generation d'un id
                   Enregistrement des attributs spatiaux correspondant */
                $attributsSpatiaux = $dataList->getAttributsSpatiaux()->toArray();
                $dataList->getAttributsSpatiaux()->clear();

                foreach ($attributsSpatiaux as $attributSpatial) {
                    $attributSpatial->setDataList($dataList);

                    if ($attributSpatial->getKeywordAttribut() == null) {
                        $attributSpatial->setKeywordAttribut(' ');
                    }
                    $em->persist($attributSpatial);
                }
                $em->flush();

                return $this->redirect($this->generateUrl('data_list_registration_confirmed', array('id' => $dataList->getId())));
            }
        }

        $jsTranslate = array(
            'formAttributes_add'    => $translator->trans('formAttributes.add'),
            'formAttributes_delete' => $translator->trans('formAttributes.delete'),
            'formAttributes_error'  => $translator->trans('formAttributes.error'),
            'spatialAttribute'      => $translator->trans('spatialAttribute')
        );

        return $this->render('data_list/form.html.twig', array(
            'form'                  => $form->createView(),
            'listMsgGetTimestamp'   => Bdd::getMsgTimestamp(),
            'title'                 => $translator->trans('datalist.edit'),
            'edit'                  => true,
            'attributsSpatiaux'     => $attributsSpatiaux,
            'jsTranslate'           => $jsTranslate
        ));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function show(DataList $dataList) {
        return $this->render('data_list/show.html.twig', array('dataList' => $dataList));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function duplicate(DataList $dataList) {

        $em = $this->doctrine->getManager();

        // Duplication de la requête
        $duplicatedDataList = clone $dataList;
        $duplicatedDataList->setNameData('Copie de ' . $dataList->getNameData());
        $duplicatedDataList->setDateData(new \DateTime());
        $em->persist($duplicatedDataList);

        // Duplication des attributs spatiaux
        foreach ($dataList->getAttributsSpatiaux() as $attributSpatial) {

            $duplicatedAttributSpatial = clone $attributSpatial;
            $duplicatedAttributSpatial->setDataList($duplicatedDataList);
            $em->persist($duplicatedAttributSpatial);
        }

        $em->flush();

        return $this->redirectToRoute('data_list_list');
    }
}
