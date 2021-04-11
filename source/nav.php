<script src="nav.js"></script>

<?php if ($_SESSION['user_is_client']) { ?>

<!-- NAV CLIENT -->
<nav>
    <a id="mobile-menu-button" onclick="dropdownButtonClicked(this)" href="javascript:void(0)"><strong>MENU</strong></a>
    
		<div id="nav-core">
			<div class="nav-part">
				<a class="dropbtn" href="courses.php">Kurzy a udalosti</a>
			</div>
		
			<div class="nav-part">
				<a class="dropbtn" href="attendance.php">Dochádzka</a>
			</div>

			<div class="nav-part">
				<a class="dropbtn" href="payments.php">Poplatky</a>
			</div>			
    </div>
    <p class="clearfix"></p>
</nav>

<?php } else { ?>

<!-- NAV ACCOUNTANT / LECTOR / ADMIN -->
<nav>
    <a id="mobile-menu-button" onclick="dropdownButtonClicked(this)" href="javascript:void(0)"><strong>MENU</strong></a>
    
		<div id="nav-core">  
		
			<?php if ($_SESSION['user_is_accountant']) { ?>
			<div class="nav-part" onmouseenter="dropdownMenuHoverEnter(this)" onmouseleave="dropdownMenuHoverLeave(this)">
					<a class="dropbtn" onclick="dropdownButtonClicked(this)" href="javascript:void(0)">Platby</a>
					<div class="dropdown-content">
							<a href="payment-item-overview.php">Prehľad položiek</a>
							<a href="payment-item-modify.php">Nová položka</a>
							<a href="payment-overview.php">Prehľad platieb</a>
							<a href="payment-modify.php">Nová platba</a>
					</div>
			</div>
			<?php } ?>

					
    </div>
    <p class="clearfix"></p>
</nav>	


<?php } ?>

<!-- NAV TEMPLATE 
<nav>
    <a id="mobile-menu-button" onclick="dropdownButtonClicked(this)" href="javascript:void(0)"><strong>MENU</strong></a>
    
		<div id="nav-core">  
			<div class="nav-part" onmouseenter="dropdownMenuHoverEnter(this)" onmouseleave="dropdownMenuHoverLeave(this)">
					<a class="dropbtn" onclick="dropdownButtonClicked(this)" href="javascript:void(0)">MenuItem1</a>
					<div class="dropdown-content">
							<a href="">SubMenu1Item1</a>
							<a href="">SubMenu1Item2</a>
							<a href="">SubMenu1Item3</a>
					</div>
			</div>

			<div class="nav-part" onmouseenter="dropdownMenuHoverEnter(this)" onmouseleave="dropdownMenuHoverLeave(this)">
					<a class="dropbtn" onclick="dropdownButtonClicked(this)" href="javascript:void(0)">MenuItem2</a>
					<div class="dropdown-content">
							<a href="">SubMenu2Item1</a>
							<a href="">SubMenu2Item2</a>
							<a href="">SubMenu2Item3</a>
					</div>
			</div>

			<div class="nav-part">
					<a class="dropbtn" href="">MenuItem3_NoSubMenu</a>
			</div>					
    </div>
    <p class="clearfix"></p>
</nav>	

-->
