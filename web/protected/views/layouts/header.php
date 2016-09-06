<nav class="uk-navbar uk-margin-large-bottom">
    <div class="uk-container uk-container-center" style="padding-top:8px;">
        <a class="uk-navbar-brand uk-hidden-small" href="#!" style="height:50px;">
            <?=CHtml::image($this->wb . '/img/logo2.png','Logo Tarsius',[
                'width'=>'140px'
            ]);?>
        </a>
        <ul class="uk-navbar-nav uk-hidden-small uk-margin-top">
            <li class="<?=$this->id=='trabalho'?'uk-active':'';?>">
                <?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?>
            </li>
            <li class="<?=$this->id=='template'?'uk-active':'';?>">
               <?=CHtml::link('Templates',$this->createUrl('/template/index'));?>
            </li>
            <li class="<?=$this->id=='avaliacao'?'uk-active':'';?>">
               <?=CHtml::link('Avaliações',$this->createUrl('/avaliacao/index'));?>
            </li>
        </ul>
        <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
        <div class="uk-navbar-brand uk-navbar-center uk-visible-small">
            <?=CHtml::image($this->wb . '/img/logo2.png','Logo Tarsius',[
                'width'=>'90px'
            ]);?>
        </div>
    </div>
</nav>