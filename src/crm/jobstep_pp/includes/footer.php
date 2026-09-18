<footer class="footer mt-auto py-2 bg-light">
	<div class="container text-center">
		<small>
			<span class="text-muted">
				©<?php 
					if(isset($languageUser)){
						echo date("Y")." ".$txtArray["Sva prava pridržana - Jobstep IT Solutions"][$languageUser];
					}else{
						echo "Sva prava pridržana - Jobstep IT Solutions";
					}
				?>
			</span>
		</small>
	</div>
</footer>