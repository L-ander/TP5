<!DOCTYPE html>
	<html lang="es">
	
		<?php include 'app/template/header.php';?>

		<!-- CSS de DataTables -->
		<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap.min.css">

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

			<!-- JS de DataTables -->
			<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
			<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap.min.js"></script>
			
		</body>

	<?php include 'app/template/footer.php'; ?>
 </html>