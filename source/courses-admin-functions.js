
// UNIT DETAILS

function load_unit_details(caller, unit_id) {
	var display = caller.nextSibling.style.display;
	if (display == '') {
		caller.nextSibling.style.display = 'none';
		caller.style.backgroundColor = '';
	} else {
		caller.style.backgroundColor = "orange";
		caller.nextSibling.style.display = '';
		caller.nextSibling.firstChild.firstChild.innerHTML = "WAIT...";		
		xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				caller.nextSibling.firstChild.firstChild.innerHTML = this.responseText;
			}
		};
		xhttp.open("GET", "ajax.php?q="+unit_id, true);
		xhttp.send();
	}
}

function update_unit_detail(caller, unit_id) {
	caller.previousElementSibling.readOnly = true;
	caller.style.backgroundColor = "orange";	
	xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			//window.alert(this.responseText);
			var xml = this.responseXML.documentElement;			
			
			var responseStatus = xml.getElementsByTagName("status")[0].childNodes[0].nodeValue;
			var responseMessage= xml.getElementsByTagName("message")[0].childNodes[0].nodeValue;
			var responseValue  = xml.getElementsByTagName("value")[0].childNodes[0].nodeValue;
			
			//window.alert(responseMessage);
			if (responseValue != '') caller.previousElementSibling.value = responseValue;
			
			if (responseStatus == 'OK') {
				caller.style.backgroundColor = "green";	
			} else {
				caller.style.backgroundColor = "red";	
			}
			
			//caller.style.backgroundColor = "green";
			caller.previousElementSibling.readOnly = false;
		}
	};
	//window.alert("ajax2.php?unitID="+unit_id+"&property="+caller.previousElementSibling.name+"&value="+caller.previousElementSibling.value);
	xhttp.open("GET", "ajax2.php?unitID="+unit_id+"&property="+caller.previousElementSibling.name+"&value="+caller.previousElementSibling.value, true);
	xhttp.send();
}

// UNIT LECTORS

function load_unit_lectors(caller, unit_id) {
	var display = caller.nextSibling.style.display;
	if (display == '') {
		caller.nextSibling.style.display = 'none';
		caller.style.backgroundColor = '';
	} else {
		caller.style.backgroundColor = "blue";
		caller.nextSibling.style.display = ''; // show nextSibling
		caller.nextSibling.firstChild.firstChild.innerHTML = "WAIT...";		
		xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				caller.nextSibling.firstChild.firstChild.innerHTML = this.responseText;
			}
		};
		xhttp.open("GET", "ajax/load_unit_lectors.php?unitID="+unit_id, true);
		xhttp.send();
	}
}

function update_unit_lector(caller, unit_id, lector_id) {
	window.alert('update lector ' + lector_id);
	caller.disabled = true;
	caller.value = "WAIT...";	
	var is_editor = caller.previousElementSibling.checked;
	//window.alert('TRY TO SSET : ' + caller.previousElementSibling.checked)
	
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			//window.alert(this.responseText);
			caller.previousElementSibling.checked = this.responseText == true ? 'true' : '';
			caller.disabled = false;
			caller.value = "Ulozit";
		}
	};
	xhttp.open("GET", "ajax/update_unit_lector.php?unitID="+unit_id+"&lectorID="+lector_id+"&is_editor="+is_editor, true);
	xhttp.send();
}

function delete_unit_lector(caller, unit_id, lector_id) {
	caller.disabled = true;
	caller.value = "WAIT...";	
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (this.responseText != 'true') {
				caller.disabled = false;
				caller.value = "Ulozit";
			}
		}
	};
	xhttp.open("GET", "ajax/delete_unit_lector.php?unitID="+unit_id+"&lectorID="+lector_id, true);
	xhttp.send();
}




// UNIT CLIENTS

function load_unit_clients(caller, unit_id) {
	var display = caller.nextSibling.style.display;
	if (display == '') {
		caller.nextSibling.style.display = 'none';
		caller.style.backgroundColor = '';
	} else {
		caller.style.backgroundColor = "blue";
		caller.nextSibling.style.display = ''; // show nextSibling
		caller.nextSibling.firstChild.firstChild.innerHTML = "WAIT...";		
		xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				caller.nextSibling.firstChild.firstChild.innerHTML = this.responseText;
			}
		};
		xhttp.open("GET", "ajax/load_unit_clients.php?unitID="+unit_id, true);
		xhttp.send();
	}
}

function update_unit_client_status(caller, unit_id, client_id, desired_status) {
	caller.disabled = true;
	
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			window.alert(this.responseText);
			//caller.previousElementSibling.checked = this.responseText == true ? 'true' : '';
			caller.disabled = false;
			
		}
	};
	//window.alert(caller +'\n'+  unit_id + '\n'+ client_id + '\n' + desired_status);
	xhttp.open("GET", "ajax/update_unit_client_status.php?unitID="+unit_id+"&clientID="+client_id+"&status="+desired_status, true);
	xhttp.send();
}


