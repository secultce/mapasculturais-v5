<?php
/** @var TYPE_NAME $config */

$this->layout = 'default';

?>

<nav class="sidebar-panel"></nav>

<div class="panel-main-content">
    <header class="panel-list panel-header">
        <h2>Configurações do plugin</h2>
    </header>
    <main>
        <table>
            <tr>
                <th>Configuração</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>Publicar parecer automaticamente ao publicar resultado da oportunidade:</td>
                <td>
                    <input
                        type="checkbox"
                        name="autopublish"
                        <?= $config->autopublish ? 'checked' : '' ?>
                    >
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
        </table>
    </main>
</div>

