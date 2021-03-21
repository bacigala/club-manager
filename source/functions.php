<?php

function header_include($headline, $extra_css = '') {
?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=0.86, maximum-scale=3">
		<link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Baloo+2&display=swap" rel="stylesheet">
		<title><?php echo ($headline == '') ? ':)' : $headline; ?></title>
		<link href="style.css" rel="stylesheet">
		<?php if ($extra_css != '') { ?>
					<link href="<?php echo($extra_css . '.css')?>" rel="stylesheet">
		<?php } ?>
		<link rel="icon" type="image/png" href="images/favicon.png">
	</head>
	<body>	
		<header>
			<h1><a href="index.php">Club manager</a></h1>
			<h2>Catchy slogan:)</h2>
			<div class="clearfix"></div>
		</header>
<?php
}
?>