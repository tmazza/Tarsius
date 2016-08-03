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

        <?php $this->renderPartial("/layouts/header"); ?>
     	<?=$content;?>

        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">
                <ul class="uk-nav uk-nav-offcanvas">
                    <?php 
                    foreach ($this->menu as $i)
                        echo '<li>' . CHtml::link($i[0],$i[1],isset($i[2])?$i[2]:[]) . '</li>';
                    ?>
                </ul>
            </div>
        </div>

    </body>
</html>