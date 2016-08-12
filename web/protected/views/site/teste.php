<script type="text/javascript">
if(typeof(EventSource) !== "undefined") {
	var source = new EventSource("<?=$this->createUrl('/site/seeder');?>");
	source.onmessage = function(event) {
		
	};
} else {
	alert('Nao suporta');
} 	
</script>