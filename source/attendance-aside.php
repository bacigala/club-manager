
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
		<p class="aside-part-headline">Info</p>
		<div class="aside-part-content">
			<p>Tu sa si môžete pozrieť dochádzku.</p>
            <p class="warning">Pre niektoré udalosti nemusí byť dochádzka zaznamenávaná.</p>
		</div>
	</div>
</aside>
