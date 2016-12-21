<?php $this->beginContent('application.views.layouts.base'); ?>
    <div class="uk-container uk-container-center uk-margin-top uk-margin-large-bottom">
        <?= HView::renderFlashes(); ?>

        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-3-4">
                <?=$content;?>
            </div>

            <div class="uk-width-medium-1-4">
                <?php if (count($this->menu)): ?>
                    <div class="uk-panel">
                        <h3 class="uk-panel-title">Ações</h3>
                        <ul class="uk-nav uk-nav-side">
                            <?php foreach ($this->menu as $i)
                                echo '<li>' . CHtml::link($i[0],$i[1],isset($i[2])?$i[2]:[]) . '</li>';?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $this->endContent(); ?>