<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\modules\evidence\models\CurrStepEvidence;
use humhub\modules\evidence\models\Evidence;

?>
<script type="text/javascript" src="<?php echo $this->context->module->assetsUrl; ?>/js/export.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->context->module->assetsUrl; ?>/css/evidence.css"/>

<div class="evidence-panel">
    <div class="container">

        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <h4> <strong>Export</strong> my evidence </h4>
                    </div>
                    <div class="col-xs-12 col-sm-6 text-right">
                        <btn class="btn btn-default" data-toggle="modal" data-target="#exportLoad"><i class="fa fa-folder-open-o fa-margin-right"></i> Load Saved Export</btn>
                        <btn class="btn btn-default" data-toggle="modal" data-target="#exportSave"><i class="fa fa-save fa-margin-right"></i> Save Export</btn>
                    </div>
                </div>
                <p><strong>Step 3 of 3</strong> - Preview evidence.<br>
                    <small>This is a preview of what will be printed out with the options to go back and edit, or export to docx.</small></p>
            </div>
        </div>

        <div class="row hidden-xs evidence-buttons-top">
            <div class="col-xs-12 col-sm-6"> <a class="btn btn-primary" href="<?= $previousUrl ?>"><i class="fa fa-arrow-left fa-margin-right"></i> Previous Step: Context</a> </div>
            <div class="col-xs-12 col-sm-6 text-right"> <a class="btn btn-primary btn-export" href="#"><i class="fa fa-download fa-margin-right"></i> Export</a> </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <h4 class="text-right date-range"><strong>Date Range -</strong> <span class="previewdate"></span></h4>

                <!-- Output Preview -->
                <div class="table-responsive" >
                    <div style="text-align:right" hidden>
                        <div>Evidence Export</div>
                        <div class="previewdate"></div>
                    </div>
                    <div class="grid-view">
                        <table class="items preview-evidence" style="border-collapse: collapse;">
                            <thead>
                            <tr style="background: #1895a4">
                                <th class="col-xs-3"><span style="color:#ffffff">APST standard description.</span></th>
                                <th class="col-xs-3 evidence"><span style="color:#ffffff">Artefact to be used as evidence.</span></th>
                                <th class="col-xs-3"><span style="color:#ffffff">Description of how the artefact demonstrates impact upon teaching and/or student learning.</span></th>
                                <th class="col-xs-3 supervisor-notes" rowspan="1"><span style="color:#ffffff">Description of how the artefact presented meets the standard described.
                                    <br><small>(Can be filled out later)</small></span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($dataObjects as $itemK => $itemV) { ?>
                                <?php foreach ($itemV as $itemKey => $itemValue) { ?>
                                    <tr class="itemTr">
                                        <td>
                                            <strong style="color:#1895a4;"><?= Evidence::getOneAPSTS($itemValue['apsts'])['title'] ?></strong>
                                            <br>
                                            <?= Evidence::getOneAPSTS($itemValue['apsts'])['descr'] ?>
                                        </td>
                                        <td class="text-left">
<!--                                            --><?php //if(!empty($itemValue['mainObject'])): ?>
                                                <?php echo Evidence::$iconObject[$itemKey]; ?> <strong><?= Evidence::$acitvityType[$itemKey]; ?></strong><br>
                                                <?= Evidence::getBody($itemValue['mainObject'], $itemKey); ?><br>

<!--                                            --><?php //endif; ?>

<!--                                            --><?php //if(!empty($itemValue['subObject'])): ?>
                                                <ul>
                                                    <?= Evidence::getPreviewUlHtml($itemValue['subObject'], $itemKey); ?>
                                                </ul>
<!--                                            --><?php //endif; ?>
                                        </td>
                                        <td class="note" style="width:50px"><?php echo $itemValue['note']; ?></td>
                                        <td class="descr" style="width:50px"></td>
                                    </tr>
                                <?php  } ?>
                            <?php  } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.Output Preview -->

            </div>
        </div>

        <div class="row evidence-buttons evidence-buttons-bottom">
            <div class="col-xs-12 col-sm-6"> <a class="btn btn-primary" href="<?= $previousUrl ?>"><i class="fa fa-arrow-left fa-margin-right"></i> Previous Step: Context</a> </div>
            <div class="col-xs-12 col-sm-6 text-right"> <a class="btn btn-primary btn-export" href="#"><i class="fa fa-download fa-margin-right"></i> Export</a> </div>
        </div>
        <div class="contentListOfItems">
            <?= (CurrStepEvidence::loadHtmlCookie())?CurrStepEvidence::loadHtmlCookie()->$step:""; ?>
        </div>
    </div>
</div>
<?= $this->render("_modals", ['step' => $step]); ?>
<script>
   var tableSaveExport = '<?= Url::toRoute("/evidence/evidence/save-to-word"); ?>';
</script>