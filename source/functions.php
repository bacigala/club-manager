<?php

function header_include($headline = 'Club manager') {
?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=0.86, maximum-scale=3">
			<link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Baloo+2&display=swap" rel="stylesheet">
			<link href="style.css" rel="stylesheet">
			<link rel="icon" type="image/png" href="images/favicon.ico">
			<title><?php echo $headline; ?></title>
		</head>
		<body>		

		
			<header>
				<!-- if user is logged-in diplay logout option -->
				<?php if (isset($_SESSION['has_user']) && $_SESSION['has_user']) { ?>
					<div id="logout-banner">
						<form method="post" id="logout-form" action="index.php">
								<input name="logout" type="submit" id="logout" value="Odhlásiť sa">
						</form>
					</div>				
				<?php }	?>
							
				<h1><a href="index.php">Club manager</a></h1>
				<h2>Catchy slogan:)</h2>
			</header>
<?php
}

function post_escaped($index) {
	return addslashes(trim(strip_tags($_POST[$index])));
}

?>	