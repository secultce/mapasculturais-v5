
<div id='draft-description-diligence' class='div-draft-description-diligence'>
    <div style='display: flex; justify-content: space-between; align-items: center;'>
        <span style='font-size: medium; color: #000'><?= $titleDraft; ?> <br /></span>
        <div>
        <?php if($titleButton !== 'expirou') : ?>
            <a class='btn btn-primary' 
                onclick='editDescription(<?php echo json_encode($resultsDraft); ?>,<?= $id; ?>)'>
                <?= $titleButton; ?>
            </a>
        <?php endif; ?>
        <?php if ($type !== 'proponent') : ?>
            <a class='btn btn-danger' 
                onclick='trashDraftDiligence(
                <?= $id; ?>,
                "Deseja excluir esse rascunho?",
                "Essa ação não pode ser desfeita. Por isso, revise essa sua ação.",
                "Não, desistir de excluir",
                "Sim, excluir",
                "btn-warning-rec",
                "btn-warning-danger"
                )'>
                <?= $titleTrash ?>
            </a>
        <?php endif; ?>
        </div>
        
    </div>
    <p style='color: #3E3E3E; font-size: 10x; margin-top: 14px;'><?php echo $resultsDraft; ?></p>
    <p style='font-size: x-small; font-size: 12px; font-weight: 700; margin-top: 8px'><?= ucfirst($dateDraft); ?></p>
    <p> 
    </p>
</div>

