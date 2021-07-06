<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Mc\DataListBundle\Entity\DataList;
use Mc\DataListBundle\Form\Type\DataListType;
use Mc\BddBundle\Controller\Bdd;
use Mc\DataSourcesBundle\Entity\DataSource;

class DataListController extends AbstractController
{
    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function index() {
        $repository = $this->getDoctrine()->getRepository('McDataListBundle:DataList');
        $list_dataList = $repository->findAll();

        return $this->render('McDataListBundle:DataList:index.html.twig', array('list_dataList' =>$list_dataList));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function new() {
        $dataList = new DataList();
        $form = $this->createForm(new DataListType, $dataList);

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                /* Enregistrement de la dataList seul -> Generation d'un id
                   Enregistrement des attributs spatiaux correspondant */
                $attributsSpatiaux = $dataList->getAttributsSpatiaux()->toArray();
                $dataList->getAttributsSpatiaux()->clear();

                $em = $this->getDoctrine()->getManager();
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
        $translator = $this->get('translator');

        $jsTranslate = array(
            'formAttributes_add'    => $translator->trans('formAttributes.add'),
            'formAttributes_delete' => $translator->trans('formAttributes.delete'),
            'formAttributes_error'  => $translator->trans('formAttributes.error'),
            'spatialAttribute'      => $translator->trans('spatialAttribute')
        );

        return $this->render('McDataListBundle:DataList:form.html.twig', array(
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
        $repository = $this->getDoctrine()->getRepository('McDataSourcesBundle:DataSource');

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
    public function queryBrut() {
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
        $button = $this->get('translator')->trans('request.button');

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
        return $this->render('McDataListBundle:DataList:confirmed.html.twig', array('dataList' => $dataList));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function delete(DataList $dataList) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($dataList);
        $em->flush();

        return $this->redirect($this->generateUrl('data_list_list'));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function edit(DataList $dataList) {
        $attributsSpatiaux = $dataList->getAttributsSpatiaux()->toArray();//array_reverse($dataList->getAttributsSpatiaux()->toArray());

        $em = $this->getDoctrine()->getManager();
        foreach ($dataList->getAttributsSpatiaux()->toArray() as $attributSpatial) {
            $em->remove($attributSpatial);
        }
        $dataList->getAttributsSpatiaux()->clear();

        $form = $this->createForm(new DataListType, $dataList);
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
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
        $translator = $this->get('translator');

        $jsTranslate = array(
            'formAttributes_add'    => $translator->trans('formAttributes.add'),
            'formAttributes_delete' => $translator->trans('formAttributes.delete'),
            'formAttributes_error'  => $translator->trans('formAttributes.error'),
            'spatialAttribute'      => $translator->trans('spatialAttribute')
        );

        return $this->render('McDataListBundle:DataList:form.html.twig', array(
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
        return $this->render('McDataListBundle:DataList:show.html.twig', array('dataList' => $dataList));
    }

    /**
     * @isGranted("ROLE_SCIENTIFIC_PLUS")
     */
    public function duplicate(DataList $dataList) {

        $em = $this->getDoctrine()->getManager();

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
