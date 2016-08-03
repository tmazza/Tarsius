<?php
$this->menu = [
	['Novo template',$this->createUrl('/template/criar')],
];
?>
<ul class="uk-list uk-list-striped">
	<?php
	foreach ($templates as $t) {
		echo "<li>{$t}</li>";
	}
	?>
</ul>
