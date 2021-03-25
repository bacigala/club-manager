<script src="nav.js"></script>

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