<nav class="uk-navbar uk-margin-large-bottom">
    <a class="uk-navbar-brand uk-hidden-small" href="#!">Tarsius</a>
    <ul class="uk-navbar-nav uk-hidden-small">
        <li>
            <?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?>
        </li>
        <li>
           <?=CHtml::link('Templates',$this->createUrl('/template/index'));?>
        </li>
        <li>
            <a href="#!">...</a>
        </li>
    </ul>
    <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
    <div class="uk-navbar-brand uk-navbar-center uk-visible-small">Tarsius</div>
</nav>