<ul class="abas clearfix">
    <li class="active">
        <a href="#diligence-principal" rel="noopener noreferrer" onclick="showRegistration()" id="tab-main-content-diligence-principal">Ficha</a>
    </li>
    <?php
    if(!$sendEvaluation): ?>
        <li class="" id="li-tab-diligence-diligence">
            <a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" id="tab-main-content-diligence-diligence">Diligência</a>
        </li>
    <?php endif; ?>
    <li class="" style="float: right;">
    <?php if($entity->canUser('evaluate')): ?>
       <button 
            type="button"
            class="btn btn-primary btn-diligence-open-active"
            id="btn-open-diligence"
            onclick="openDiligence(0)"            
        >
            Abrir Diligência
        </button>
    <?php endif; ?>
    </li>
</ul>