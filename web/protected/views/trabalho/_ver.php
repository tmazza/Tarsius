Status: <?=$trabalho->getLabelStatus();?>
<table class="uk-table uk-table-condensed">
  <?php 
  foreach ($trabalho->processos as $p){
	echo '<tr>';	
	echo "<td style='width:60px;'>#{$p->id}</td>";  		
	echo "<td style='width:60px;'>{$p->pid}</td>";  		
	echo "<td style='width:60px;'>{$p->workDir}</td>";  		
  	if($p->status == 1){
		echo "<td>";
       	if(isset($faltaProcessar[$p->id])){
       		echo '<div class="uk-grid">';
       			echo "<div class='uk-width-9-10'>";
					echo "<progress value='" . (100 - (100 * ($faltaProcessar[$p->id]/$p->qtd))) . "' max='100' style='width:90%'></progress>";
       			echo "</div>";
       			echo "<div class='uk-width-1-10'>";
					echo $p->qtd-$faltaProcessar[$p->id] .'/'.$p->qtd;
       			echo "</div>";
			echo '</div>';
       	}
		echo "</td>";  		
  	} else {
		echo "<td><small>Finalizado</small></td>";  		
  	}
	echo '</tr>';
  }
  ?>
</table>