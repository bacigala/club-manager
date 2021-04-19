<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('functions.php');
	include('db.php');
  include('login-verify.php'); // login check
	header_include();

	if (isset($_SESSION['has_user']) && $_SESSION['has_user']) {
		// user logged-in
		include('nav.php');	
?>

<section>
    <h1>Welcome to the main page!</h1>
		<div id="sectionh1negativemarginfix"></div>
		<h2>Section1</h2>
    <ul>
        <li>List item1</li>
        <li>List item2</li>
        <li>List item3</li>
    </ul>
		
		<h2>Section2</h2>
		<p class="success">Success banner.<p>
		<p class="info">Info banner.<p>
		<p class="warning">Warning banner.<p>
		<p class="error">Error banner.<p>
		
    <h2>Section3</h2>
		<p>Donec quis sapien vehicula, viverra sem at, condimentum ex. Suspendisse a tortor in neque ultrices pretium scelerisque at orci. Vivamus id nisi vitae ex imperdiet bibendum interdum eu elit. Quisque erat felis, tincidunt vel mauris vel, tempus vehicula nulla. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Integer dapibus fermentum consequat. Maecenas facilisis, enim at pharetra tincidunt, dolor odio finibus dolor, non posuere quam lacus eget nisi. Aenean consequat ultrices ante eget rhoncus. Donec dictum velit id ex pulvinar, et ullamcorper nisi dignissim. Nunc laoreet aliquet malesuada. Praesent rhoncus ante augue, nec bibendum purus luctus in. Praesent lectus lectus, iaculis eget placerat ultrices, ultricies nec arcu. Integer ac fermentum mi. Fusce cursus elementum orci, quis suscipit elit sollicitudin non. Pellentesque sed arcu ipsum.</p>
</section>

<?php
		include('index-aside.php');
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>