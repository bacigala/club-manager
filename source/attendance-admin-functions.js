
/**
 * toogle present / not present when chceckbox is (un)chcecked
 */
function toogle_present(caller) {
	caller.disabled = true;
	// ajax - toogle presence
	let xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') {
				window.alert("ERROR\nAkciu sa nepodarilo vykonat\n" + this.responseText)
				caller.checked = !caller.checked;
			}
			caller.disabled = false;
		}
	};
	xhttp.open("GET", "ajax/attendance-update.php?id="+caller.id+"&value="+caller.checked, true);
	xhttp.send();
}
