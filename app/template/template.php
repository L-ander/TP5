<!DOCTYPE html>
	<html lang="es">
	
		<?php include 'app/template/header.php';?>

		<!-- CSS de DataTables -->
		<link rel="stylesheet" href="resources/library/plugins/jquery-datatable/1.13.6/css/dataTables.bootstrap.min.css">

		<body class="theme-teal">
			<script src="resources/library/plugins/jquery-datatable/1.13.6/js/jquery.dataTables.min.js"></script>
			<script src="resources/library/plugins/jquery-datatable/1.13.6/js/dataTables.bootstrap.min.js"></script>
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

	<?php include 'app/template/footer.php'; ?>
 </html>