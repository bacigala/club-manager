
/**
 * Update account prilege (from account-overview).
 */
function update_privilege(caller, account_id, privilege) {
	// ajax
	caller.disabled = true;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') {
				window.alert("ERROR\nAkciu sa nepodarilo vykonať.\n" + this.responseText)
				caller.checked = !caller.checked;
			}
			caller.disabled = false;
		}
	};
	xhttp.open("POST", "ajax/account-privilege-update.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("id="+account_id+"&key="+privilege+"&value="+caller.checked);
}
