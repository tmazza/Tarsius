<h2>Trabalhos ativos</h2>
<hr><br>
<?php if (count($trabalhos) > 0): ?>
    <ul>
        <?php foreach ($trabalhos as $t): ?>
            <li>
                <a href='<?=$this->createUrl('/trabalho/ver',[
                    'id' => $t->id,
                ]);?>' class='uk-button uk-button-link'>
                    <?=$t->nome?>
                </a>
                <?php if($t->status == Trabalho::statusExecutando): ?>
                    <div class="uk-badge uk-badge-success">Executando</div>
                <?php else: ?>
                    <div class="uk-badge uk-badge-warning">Parando...</div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <div class="uk-alert">
        Nenhum trabalho ativo no momento.        
    </div>
<?php endif; ?>