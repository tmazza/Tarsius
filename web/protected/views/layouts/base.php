<!DOCTYPE html>
<html lang="pt-br" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tarsius</title>
        <link rel="stylesheet" href="<?=$this->wb;?>/uikit/css/uikit.min.css">

        <link rel="icon" type="image/png" sizes="16x16" href="<?=$this->wb?>/img/favicon.png?v1">

        <script src="<?=$this->wb;?>/uikit/js/uikit.min.js"></script>
    </head>

    <body>

        <?php $this->renderPartial("/layouts/header"); ?>
     	<?=$content;?>

        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">
                <ul class="uk-nav uk-nav-offcanvas">
                    <?php
                    echo CHtml::tag('li', [], CHtml::link('Trabalhos', $this->createUrl('/trabalho/index')));
                    echo CHtml::tag('li', [], CHtml::link('Templates', $this->createUrl('/template/index')));
                    ?>
                    <li class="uk-nav-divider"></li>
                    <?php 
                    foreach ($this->menu as $i)
                        echo '<li>' . CHtml::link($i[0],$i[1],isset($i[2])?$i[2]:[]) . '</li>';
                    ?>
                </ul>
            </div>
        </div>

    </body>
</html>