
<aside>
	<div class="aside-part">	
		<p class="aside-part-headline">Prihlásený používateľ</p>
		<div class="aside-part-content">
			<ul>
				<li><?php echo $_SESSION['user_name'] . ' ' . $_SESSION['user_surname'];?></li>
			</ul>
		</div>
	</div>
	
	<div class="aside-part">	
		<p class="aside-part-headline">Kredit</p>
		<div class="aside-part-content">
			<p><?php echo get_credit_balance($mysqli);?> €</p>
            <button class="button-create-new" type="button" onclick="buy_credit()">Kúpiť kredit</button>
		</div>
	</div>

	<div class="aside-part">
		<p class="aside-part-headline">Info</p>
		<div class="aside-part-content">
			<p>Tu sú zaznamenané platby.</p>
		</div>
	</div>
</aside>
