<div>
    <button
        class="btn-primary showOpinion"
        data-id="<?= isset($registration) ? $registration->id : '{{data.registration.id}}'?>"
        ng-if="data.registration.status != 0"
        onclick="showOpinions(this.getAttribute('data-id'))"
    ><?= \MapasCulturais\i::__('Visualizar Pareceres') ?></button>
</div>