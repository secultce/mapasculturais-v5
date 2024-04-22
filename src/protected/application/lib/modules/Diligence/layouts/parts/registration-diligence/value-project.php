<?php //dump($entity); ?>
<label style="font-weight: bold; font-size: smaller;">
   <?php \MapasCulturais\i::_e(" O projeto está autorizado ou não autorizado?");?>  
</label>
<p>
   <select id="select-value-project-diligence" placeholder="Selecione" style="width: 100%;">
      <option value="">Selecione um status</option>
      <option value="Não">Não autorizado</option>
      <option value="Sim">Autorizado</option>
   </select>
   
</p>
<p id="paragraph_value_project">
<label style="font-weight: bold; font-size: small;">
   <?php \MapasCulturais\i::_e(" Valor destinado ao projeto");?>
</label>
<input type="text" id="input-value-project-diligence"  class="js-mask-currency"  placeholder="ex: R$ 100.000"  style="width: 100%;">
</p>
