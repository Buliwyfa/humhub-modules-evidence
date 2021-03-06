<?php

/**
 * Connected Communities Initiative
 * Copyright (C) 2016  Queensland University of Technology
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.org/licences GNU AGPL v3
 *
 */

class EvidenceController extends Controller
{

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('prepare', 'sectionPrepareWord', 'saveToWord', 'saveExport', 'loadExport', 'sectionPreview', 'saveCurrentHtml', 'deleteExport'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}



	public function actionSaveToWord()
	{
		$evidence = new Evidence;
		$path = $evidence->prepareHtmlToHtml($_POST['table'])->saveWord();
		echo json_encode(['flag' => 1, 'path' => $path]);
	}

	/**
	 * First step
	 */
	public function actionPrepare()
	{
		Yii::import("application.modules.evidence.models.Evidence");
		$rawData = Evidence::instance()->getAllQuery()->filterActivity()->addEntryMessageActivity()->getData();
		$dataProvider = new CArrayDataProvider($rawData, [
			'sort'=>array(
				'attributes'=>array(
					"created_at" => [
						'desc' => 'created_at desc'
					]
				),
			),
		]);

		$this->render('index', array(
			'dataProvider'=>$dataProvider,
			'step' => ExportStepEvidence::STEP1,
			'stepUrl' => Yii::app()->createUrl("/evidence/evidence/sectionPrepareWord"),
		));
	}

	/**
	 * Second step
	 */
	public function actionSectionPrepareWord()
	{
		if((!empty($_POST) && isset($_POST['activityItems']) || true)) {
			$data = CurrStepEvidence::loadHtmlCookie();
			$itemsList = isset($_POST['activityItems'])?$_POST['activityItems']:json_decode($data->obj_step1, true);
			CurrStepEvidence::setCurrentStep(null, json_encode($itemsList), ExportStepEvidence::STEP1);
			$dataObjects = Evidence::getPrepareObjects($itemsList);
			$this->render("displayContext", [
				'dataObjects' => $dataObjects,
				'step' => ExportStepEvidence::STEP2,
				'stepUrl' => Yii::app()->createUrl("/evidence/evidence/sectionPreview"),
				'previousUrl' => Yii::app()->createUrl("/evidence/evidence/prepare"),
			]);
		} else {
			return $this->redirect(Yii::app()->createUrl("/evidence/evidence/prepare"));
		}
	}

	/**
	 * Third step
	 */
	public function actionSectionPreview()
	{
		User::model()->findByPk(Yii::app()->user->id);
		if((!empty($_POST) && isset($_POST['activityItems'])) || true) {
			$data = CurrStepEvidence::loadHtmlCookie();
			$itemsList = isset($_POST['activityItems'])?$_POST['activityItems']:json_decode($data->obj_step2, true);
			CurrStepEvidence::setCurrentStep(null, json_encode($itemsList), ExportStepEvidence::STEP2);
			$dataObjects = Evidence::getPreparePreivew($itemsList);
			$this->render("preview", [
				'dataObjects' => $dataObjects,
				'step' => ExportStepEvidence::STEP1,
				'previousUrl' => Yii::app()->createUrl("/evidence/evidence/sectionPrepareWord"),
			]);
		} else {
			return $this->redirect(Yii::app()->createUrl("/evidence/evidence/prepare"));
		}
	}

	public function actionSaveExport()
	{
		$output = [];
		parse_str($_POST['exportData'], $output); // here $step and $saveExport
		parse_str($_POST['obj_data'], $dataObj);

		$currentStep = $output['step'];
		$exportName = $output['saveExport'];
		$exportData = $_POST['html'];
		$exportObjData = json_encode($dataObj['activityItems']);
		CurrStepEvidence::setCurrentStep($exportData, $exportObjData, $currentStep);
		ExportStepEvidence::saveExport($exportName);
		setcookie("LoadExport", 1, time()+3600*24*10, "/");
		echo json_encode(['flag' => true, 'redirect' => Yii::app()->request->urlReferrer]);
	}

	public function actionLoadExport()
	{
		$exportId = $_POST['exportId'];
		$model = ExportStepEvidence::model()->find('id='. $exportId);
		if(!empty($model)) {
			CurrStepEvidence::getDataFromLoadExport($model);
			setcookie("LoadExport", 1, time()+3600*24*10, "/");
			echo json_encode(['flag' => true, 'redirect' => Yii::app()->createUrl("/evidence/evidence/prepare")]);
			return;
		}

		echo json_encode(['flag' => false, 'error' => 'Export by current name not found']);
	}

	public function actionSaveCurrentHtml()
	{
		if(isset($_POST['html']) && !empty($_POST['html'])) {
			parse_str($_POST['exportData'], $output); // here $step and $saveExport

			$currentStep = $output['step'];
			$exportData = $_POST['html'];
			CurrStepEvidence::setCurrentStep($exportData, null, $currentStep);
		}
	}

	public function actionDeleteExport()
	{
		if(isset($_POST['id']) && !empty($_POST)) {
			ExportStepEvidence::model()->deleteByPk($_POST['id']);
		}
	}
}
