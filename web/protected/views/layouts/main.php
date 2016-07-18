<!DOCTYPE html>
<html lang="pt-br" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tarsius</title>
        <link rel="apple-touch-icon-precomposed" href="images/apple-touch-icon.png">
        <link rel="stylesheet" href="<?=$this->wb;?>/uikit/css/uikit.min.css">
        <script src="<?=$this->wb;?>/uikit/js/uikit.min.js"></script>
    </head>

    <body>

        <div class="uk-container uk-container-center uk-margin-top uk-margin-large-bottom">

            <nav class="uk-navbar uk-margin-large-bottom">
                <a class="uk-navbar-brand uk-hidden-small" href="#!">Tarsius</a>
                <ul class="uk-navbar-nav uk-hidden-small">
                    <li>
                        <?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?>
                    </li>
                    <li>
                        <a href="#!">Templates</a>
                    </li>
                    <li>
                        <a href="#!">...</a>
                    </li>
                </ul>
                <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
                <div class="uk-navbar-brand uk-navbar-center uk-visible-small">Brand</div>
            </nav>

            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-3-4">
                	<?=$content;?>
                </div>

                <div class="uk-width-medium-1-4">
                    <div class="uk-panel">
                        <h3 class="uk-panel-title">Ações</h3>
                        <ul class="uk-list uk-list-line">
                            <li><?=CHtml::link('Novo trabalho',$this->createUrl('/trabalho/novo'));?></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">
                <ul class="uk-nav uk-nav-offcanvas">
                    <li>
                        <a href="layouts_frontpage.html">Frontpage</a>
                    </li>
                    <li>
                        <a href="layouts_portfolio.html">Portfolio</a>
                    </li>
                    <li class="uk-active">
                        <a href="layouts_blog.html">Blog</a>
                    </li>
                    <li>
                        <a href="layouts_documentation.html">Documentation</a>
                    </li>
                    <li>
                        <a href="layouts_contact.html">Contact</a>
                    </li>
                    <li>
                        <a href="layouts_login.html">Login</a>
                    </li>
                </ul>
            </div>
        </div>

    </body>
</html>