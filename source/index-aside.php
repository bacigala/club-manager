<aside>
	<div class="aside-part">	
		<p class="aside-part-headline">Prihlásený používateľ</p>
		<div class="aside-part-content">
			<ul>
				<li><strong>Meno:</strong> <?php echo $_SESSION['user_name'] . ' ' . $_SESSION['user_id'];?></li>
				<li><strong>Priezvisko:</strong> <?php echo $_SESSION['user_surname'];?></li>
				<li><strong>Admin:</strong> <?php echo !$_SESSION['user_is_client'] ? 'ano' : 'nie';?></li>
			</ul>
		</div>
	</div>
	
	<div class="aside-part">	
		<p class="aside-part-headline">AsidePart</p>
		<div class="aside-part-content">
			<p>Ut sit amet ex sit amet mi accumsan tincidunt. Donec id venenatis turpis, a tempus ex. Quisque quis massa sed felis venenatis volutpat nec blandit nisi. Interdum et malesuada fames ac ante ipsum primis in faucibus. Maecenas eu ipsum sem. Etiam faucibus ante mauris, eu dapibus quam ornare id. Nullam a porttitor quam, nec tincidunt metus.</p>
			<p class="info">Info banner in aside part.</p>
		</div>
	</div>
</aside>
