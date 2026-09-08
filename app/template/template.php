<!DOCTYPE html>
<html lang="es">
	<?php

	  include 'app/template/header.php';
	?>
    <body class="theme-teal">
    	<!-- Page Loader -->
		<!-- <div class="page-loader-wrapper">
		  <div class="loader">
		    <div class="preloader">
		      <div class="spinner-layer pl-teal">
		        <div class="circle-clipper left">
		          <div class="circle"></div>
		        </div>
		        <div class="circle-clipper right">
		          <div class="circle"></div>
		        </div>
		      </div>
		    </div>
		    <p>Por Favor Espere...</p>
		  </div>
		</div> -->
		<!-- #END# Page Loader -->

    	<?php

	        $enlacesController = new EnlacesController();
	        $result = $enlacesController->enlacesControl();

	    ?>


	</body>

	<?php

		include 'app/template/footer.php';
	?>
 </html>