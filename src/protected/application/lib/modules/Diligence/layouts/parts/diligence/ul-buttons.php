<ul class="abas clearfix">
    <li class="active"  id="li-tab-ficha-diligence">
        <a href="#registration-content-all" rel="noopener noreferrer" onclick="showRegistration()" id="">Ficha</a>
    </li>
    <?php
    if (!$sendEvaluation) : ?>
        <li class="" id="li-tab-diligence-diligence">
            <a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" id="">Diligência</a>
        </li>
    <?php endif; ?>
    <li class="" style="float: right;">
        <button type="button"
                class="btn btn-primary btn-diligence-open-active"
                id="btn-open-diligence"
                onclick="openDiligence(0)">
            Abrir Diligência
        </button>
    </li>
</ul>