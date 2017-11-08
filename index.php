<?php

	require_once ('assets/required/config.php');

	// Get the value of the $page variable from the url, if empty, set it to index to load the home page
	$page = (isset($_GET['page']) ? $_GET['page'] : 'home');
	if (!file_exists("pages/$page.php")) {
		$page = "notfound";
	}
	
	// Begin
	// To be swapped for a database load event
	$pageTitle = ucfirst($page);
	$pageTitle .= " | ";
	$pageTitle .= constant("SITE_TITLE");
	// End

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="assets/themes/clean-and-elegant/main.css" />
		<title><?php echo $pageTitle; ?></title>
	</head>
	<body>
		<header>
			<?php require_once ('assets/required/navigation.php'); ?>
		</header>
		<div class="container">
			<!-- Begin dynamically loaded page -->
			<?php

				//  Check if requested page exsists, if so, load that page, otherwise load 404. If nothing is specified, we load the index page as can be seen in the if statement at the top of the page
				require("pages/$page.php");

			?>
			<!-- End dynamically loaded page -->
		</div>
		<footer class="py-5" style="margin-top: 5rem; color: #fff">
			<div class="container">
				<div class="row">
					<div class="col-sm-10">
						<p><?php echo SITE_TITLE; ?> &copy; <?php echo date('Y'); ?></p>
						<p>This website (<?php echo SITE_TITLE; ?>) is a member of the Amazon Services LLC Associates Program.</p>
					</div>
					<div class="col-sm-2 text-right">
						<p>
							<a href="https://facebook.com/<?php echo FACEBOOK; ?>" target="_blank">Facebook</a>
						</p>
					</div>
				</div>
			</div>
		</footer>
	</body>
</html>