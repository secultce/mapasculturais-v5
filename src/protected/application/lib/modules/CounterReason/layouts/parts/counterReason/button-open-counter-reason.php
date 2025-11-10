<div class="opportunity-claim-button">
    <a class="btn btn-success btnOpen-counterReason" id="btnOpen-counterReason-<?= $entity->id; ?>"
        data-entity-id-cr="<?= $entity->id; ?>" href="javascript:void(0)"
        data-entity-context-cr="<?= htmlspecialchars($cr->text ?? '', ENT_QUOTES); ?>"
    >
        <?= $labelButton; ?>
    </a>
</div>
