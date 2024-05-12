
<div id='draft-description-diligence' class='div-draft-description-diligence'>
    <div style='display: flex; justify-content: space-between;'>
        <span style='font-size: medium; color: #000'><?= $titleDraft; ?> <br /></span>
        <a class='btn btn-primary' 
            onclick='editDescription(<?php echo json_encode($resultsDraft); ?>,<?= $id; ?>)'>
            <?= $titleButton; ?>
        </a>
    </div>
    <p style='color: #3E3E3E; font-size: 10x; margin-top: 14px;'><?php echo $resultsDraft; ?></p>
    <p style='font-size: x-small; font-size: 12px; font-weight: 700; margin-top: 8px'><?= ucfirst($dateDraft); ?></p>
    <p> 
    </p>
</div>