


/**
 * Create new course request.
 */
function create_unit(type) {
	let name = window.prompt("Názov:", "");
	if (name === null) return;

	// ajax - create new coursse
	let xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			let newUnitId = parseInt(this.responseText);
			if (isNaN(newUnitId)) {
				window.alert("ERROR\n" + this.responseText)
				return;
			}
			window.location.href = "unit-admin-overview.php?unitId=" + newUnitId;
		}
	};
	xhttp.open("GET", "ajax/unit-insert.php?unitName="+name+"&unitType="+type, true);
	xhttp.send();
}

// create ocurrence button click
function create_ocurrence(caller, unit_id) {
	caller.enabled = false;
	event.stopPropagation();
	let name = window.prompt("Názov:", unit_id);
	if (name === null) return;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '')
				window.alert("CHYBA!\nVýkyt sa nepodarilo vytvoriť.\n" + this.responseText);
			caller.enabled = true;
			window.alert("RESULT: \n" + this.responseText);

			// refresh lists
			let target_elements = document.getElementsByClassName("unit_"+unit_id+"_ocurrences");
			Array.prototype.forEach.call(target_elements, function(element) {
				// todo
			});
		}
	};
	xhttp.open("GET", "ajax/ocurrence-create.php?unitID="+unit_id+"&name="+name, true);
	xhttp.send();
}




/*
 * GENERAL UNIT DETAILS & FUNCTIONS
 */

/**
 * Called by click on unit in admin-unit-ovreview
 * Ajax call to render unit details in next table row.
 */
function load_unit_details(caller, unit_id, target = null) {
	let display = caller.nextSibling.style.display;
	if (display === '') {
		caller.nextSibling.style.display = 'none';
		caller.style.backgroundColor = '';
		caller.style.borderBottomColor = "black";
	} else {
		caller.style.backgroundColor = "greenyellow";
		caller.style.borderBottomColor = "greenyellow";
		caller.nextSibling.style.display = '';
		caller.nextSibling.firstChild.firstChild.innerHTML = "WAIT...";
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState === 4 && this.status === 200) {
				// ajax ok
				caller.nextSibling.firstChild.firstChild.innerHTML = this.responseText;
			}
		};
		xhttp.open("GET", "ajax/unit-detail-load.php?q="+unit_id, true);
		xhttp.send();
	}
}

/**
 * Update general unit details (name, capacity, venue...)
 */
function update_unit_detail(caller, unit_id) {
	caller.previousElementSibling.readOnly = true;
	//caller.style.backgroundColor = "orange";
	let xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			//window.alert(this.responseText);
			// ajax ok
			let xml = this.responseXML.documentElement;

			let responseStatus = xml.getElementsByTagName("status")[0].childNodes[0].nodeValue;
			//let responseMessage= xml.getElementsByTagName("message")[0].childNodes[0].nodeValue;
			let responseValue  = xml.getElementsByTagName("value")[0].childNodes[0].nodeValue;

			if (responseValue !== '') caller.previousElementSibling.value = responseValue;
			caller.previousElementSibling.style.backgroundColor = responseStatus === 'OK' ? "#7aff68" : 'red';
			window.setTimeout(function() {caller.previousElementSibling.style.backgroundColor = 'transparent';}, 2000);
			caller.previousElementSibling.readOnly = false;

			// update all elements holding updated value
			let target_elements = document.getElementsByClassName("unit_"+unit_id+"_"+caller.previousElementSibling.name+"_label");
			Array.prototype.forEach.call(target_elements, function(element) {
				element.innerHTML = responseValue;
			});
		}
	};
	xhttp.open("GET", "ajax/unit-detail-update.php?unitID="+unit_id+"&property="+caller.previousElementSibling.name+"&value="+caller.previousElementSibling.value, true);
	xhttp.send();
}

/**
 * Deletes the unit, offers choice to deleta all or just 'close'.
 * @param unit_id
 */
function unit_delete(unit_id) {
	//todo
	window.alert("delete unit " + unit_id);
}



/*
 * UNIT LECTORS
 */

// called by clicking on "Lectors" <tr> - show/hide lector list
function load_unit_lectors(caller, target, unit_id) {
	const display = target.style.display;
	if (display === '') {
		target.style.display = 'none';
		caller.style.backgroundColor = '';
	} else {
		caller.style.backgroundColor = "mediumseagreen";
		target.style.display = ''; // show
		insert_unit_lectors_table(unit_id, target.firstChild.firstChild); //div for lector list
	}
}

// inserts table of unit lecotrs in target.innerHTML
function insert_unit_lectors_table(unit_id, target) {
	target.innerHTML = "Loading...";
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			target.innerHTML = this.responseText;
			setup_lector_autocomplete(unit_id);
		}
	};
	xhttp.open("GET", "ajax/unit-lector-load.php?unitID=" + unit_id, true);
	xhttp.send();
}

// refresh lector list of given unit
function refresh_lector_list(unit_id) {
	let target = document.getElementsByClassName("unit" + unit_id + "lectorListContainer");
	Array.prototype.forEach.call(target, function (t) {
		t.innerHTML = "Loading...";
	});
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			let target = document.getElementsByClassName("unit" + unit_id + "lectorListContainer");
			let that = this;
			Array.prototype.forEach.call(target, function (t) {
				t.innerHTML = that.responseText;
				setup_lector_autocomplete(unit_id);
			});
		}
	};
	xhttp.open("GET", "ajax/unit-lector-load.php?unitID=" + unit_id, true);
	xhttp.send();
}

/**
 * Loads aailable lectors (not added to unit yet)
 * @param unit_id
 */
function setup_lector_autocomplete(unit_id) {
	let xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			// ajax ok
			if (this.responseText === '') return;

			// parse received XML
			let xml = this.responseXML.documentElement;

			let names = xml.getElementsByTagName("lector");
			let suggestion = [];
			for (let i = 0; i < names.length; i++)
				suggestion.push(names[i].childNodes[0].nodeValue);

			let ids = xml.getElementsByTagName("id");
			let suggestion_id = [];
			for (let i = 0; i < ids.length; i++)
				suggestion_id.push(ids[i].childNodes[0].nodeValue);


			// 'activate' autocomplete for each lector-search-input of this unit
			let autocomplete_elements = document.getElementsByClassName("lectorSearch" + unit_id);
			Array.prototype.forEach.call(autocomplete_elements, function(element) {
				autocomplete_setup(element, suggestion, suggestion_id, unit_id, 'lector');
			});
		}
	};
	xhttp.open("GET", "ajax/unit-lector-suggestion.php?unitID="+unit_id, true);
	xhttp.send();
}

function update_unit_lector(caller, unit_id, lector_id) {
	caller.disabled = true;
	caller.value = "WAIT...";
	let is_editor = caller.previousElementSibling.checked;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			// ajax ok
			refresh_lector_list(unit_id);
		}
	};
	xhttp.open("GET", "ajax/unit-lector-update.php?unitID="+unit_id+"&lectorID="+lector_id+"&is_editor="+is_editor, true);
	xhttp.send();
}

function delete_unit_lector(caller, unit_id, lector_id) {
	caller.disabled = true;
	caller.value = "WAIT...";
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText === 'true') {
				caller.disabled = false;
				refresh_lector_list(unit_id);
				caller.value = "Ulozit";
			} else {
				window.alert(this.responseText);
			}
		}
	};
	xhttp.open("GET", "ajax/unit-lector-delete.php?unitID="+unit_id+"&lectorID="+lector_id, true);
	xhttp.send();
}



/*
 * UNIT CLIENTS
 */

function load_unit_clients(caller, unit_id) {
	let display = caller.nextSibling.style.display;
	if (display === '') {
		caller.nextSibling.style.display = 'none';
		caller.style.backgroundColor = '';
	} else {
		caller.style.backgroundColor = "mediumseagreen";
		caller.nextSibling.style.display = ''; // show nextSibling
		caller.nextSibling.firstChild.firstChild.innerHTML = "WAIT...";
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState === 4 && this.status === 200) {
				caller.nextSibling.firstChild.firstChild.innerHTML = this.responseText;
				setup_client_autocomplete(unit_id);
			}
		};
		xhttp.open("GET", "ajax/unit-client-load.php?unitID="+unit_id, true);
		xhttp.send();
	}
}

function insert_unit_clients_table(unit_id, target) {
	target.innerHTML = "Loading...";
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			target.innerHTML = this.responseText;
			setup_client_autocomplete(unit_id);
		}
	};
	xhttp.open("GET", "ajax/unit-client-load.php?unitID="+unit_id, true);
	xhttp.send();
}

/**
 * Loads aailable clients (not added or invited to unit yet)
 * @param unit_id
 */
function setup_client_autocomplete(unit_id) {
	let xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			// ajax ok
			if (this.responseText === '') return;

			// parse received XML
			let xml = this.responseXML.documentElement;

			let names = xml.getElementsByTagName("client");
			let suggestion = [];
			for (let i = 0; i < names.length; i++)
				suggestion.push(names[i].childNodes[0].nodeValue);

			let ids = xml.getElementsByTagName("id");
			let suggestion_id = [];
			for (let i = 0; i < ids.length; i++)
				suggestion_id.push(ids[i].childNodes[0].nodeValue);


			// 'activate' autocomplete for each client-search-input of this unit
			let autocomplete_elements = document.getElementsByClassName("clientSearch" + unit_id);
			Array.prototype.forEach.call(autocomplete_elements, function(element) {
				autocomplete_setup(element, suggestion, suggestion_id, unit_id);
			});
		}
	};
	xhttp.open("GET", "ajax/unit-client-suggestion.php?unitID="+unit_id, true);
	xhttp.send();
}

function update_unit_client_status(caller, unit_id, client_id, desired_status) {
	caller.disabled = true;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			// ajax ok
			caller.disabled = false;
			refresh_client_list(unit_id)
		}
	};
	xhttp.open("GET", "ajax/unit-client-update.php?unitID="+unit_id+"&clientID="+client_id+"&status="+desired_status, true);
	xhttp.send();
}

// function to be called on "client add/invite" button click
function unit_add_client(client_id, unit_id, status = 'manual') {
	// ajax query to add user to unit
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') {
				// query error
				window.alert("ERROR\n" + this.responseText);
				return;
			}
			// ajax ok -> refresh client lists
			refresh_client_list(unit_id);
		}
	};
	xhttp.open("GET", "ajax/unit-client-insert.php?unitID="+unit_id+"&clientID="+client_id+"&status="+status, true);
	xhttp.send();
}

// refresh client list of given unit
function refresh_client_list(unit_id) {
	let target = document.getElementsByClassName("unit" + unit_id + "clientListContainer");
	Array.prototype.forEach.call(target, function (t) {
		insert_unit_clients_table(unit_id, t);
	});
}


/*
 * UNIT EVENTS (EVENTS OF COURSES / OCCURENCES OF EVENTS)
 */

// called by clicking on "Events" <tr> - show/hide event list
function load_unit_events(caller, target, unit_id, type) {
	const display = target.style.display;
	if (display === '') {
		target.style.display = 'none';
		caller.style.backgroundColor = '';
	} else {
		caller.style.backgroundColor = "mediumseagreen";
		target.style.display = ''; // show
		insert_unit_events_table(unit_id, target.firstChild.firstChild, type); //div for event list
	}
}

// inserts table of unit lecotrs in target.innerHTML
function insert_unit_events_table(unit_id, target, type) {
	target.innerHTML = "Loading...";
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			target.innerHTML = this.responseText;
			setup_unit_autocomplete(unit_id);
		}
	};
	xhttp.open("GET", "ajax/unit-event-load.php?unitID=" + unit_id + "&type=" + type, true);
	xhttp.send();
}

// refresh lector list of given unit
function refresh_unit_list(unit_id) {
	let target = document.getElementsByClassName("unit" + unit_id + "unitListContainer");
	Array.prototype.forEach.call(target, function (t) {
		t.innerHTML = "Loading...";
	});
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			let target = document.getElementsByClassName("unit" + unit_id + "unitListContainer");
			let that = this;
			Array.prototype.forEach.call(target, function (t) {
				t.innerHTML = that.responseText;
				setup_unit_autocomplete(unit_id);
			});
		}
	};
	xhttp.open("GET", "ajax/unit-event-load.php?unitID=" + unit_id, true);
	xhttp.send();
}

/**
 * Loads aailable lectors (not added to unit yet)
 * @param unit_id
 */
function setup_unit_autocomplete(unit_id) {
	let xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			// ajax ok
			//window.alert("setup_unit_autocomplete\n" + this.responseText);
			if (this.responseText === '') return;


			// parse received XML
			let xml = this.responseXML.documentElement;

			let names = xml.getElementsByTagName("unit");
			let suggestion = [];
			for (let i = 0; i < names.length; i++)
				suggestion.push(names[i].childNodes[0].nodeValue);

			let ids = xml.getElementsByTagName("id");
			let suggestion_id = [];
			for (let i = 0; i < ids.length; i++)
				suggestion_id.push(ids[i].childNodes[0].nodeValue);


			// 'activate' autocomplete for each lector-search-input of this unit
			let autocomplete_elements = document.getElementsByClassName("unitSearch" + unit_id);
			//window.alert(autocomplete_elements.length + "\nsearch for: " + "unitSearch" + unit_id);
			Array.prototype.forEach.call(autocomplete_elements, function(element) {
				autocomplete_setup(element, suggestion, suggestion_id, unit_id, 'unit');
				//window.alert("autocomplete setup " + unit_id);
			});
		}
	};
	xhttp.open("GET", "ajax/unit-unit-suggestion.php?unitID="+unit_id, true);
	xhttp.send();
}

function update_unit_unit(caller, unit_id, lector_id) {
	caller.disabled = true;
	caller.value = "WAIT...";
	let is_editor = caller.previousElementSibling.checked;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			// ajax ok
			refresh_lector_list(unit_id);
		}
	};
	xhttp.open("GET", "ajax/unit-lector-update.php?unitID="+unit_id+"&lectorID="+lector_id+"&is_editor="+is_editor, true);
	xhttp.send();
}

function delete_unit_unit(caller, unit_id, lector_id) {
	caller.disabled = true;
	caller.value = "WAIT...";
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText === 'true') {
				caller.disabled = false;
				refresh_lector_list(unit_id);
				caller.value = "Ulozit";
			} else {
				window.alert(this.responseText);
			}
		}
	};
	xhttp.open("GET", "ajax/unit-lector-delete.php?unitID="+unit_id+"&lectorID="+lector_id, true);
	xhttp.send();
}



/*
 * AUTOCOMPLETE COMPONENT
 */

// attach autocomplete div to input field
function autocomplete_setup(input, suggestions, suggestions_ids, unit_id, type = 'client') {
	let currentSuggestionFocus = -1;
	input.addEventListener("input", function(e) {
		closeAllLists(); // close other autocomplete lists
		let val = this.value;
		if (!val) return false;
		currentSuggestionFocus = -1;

		let suggestionDiv = document.createElement("DIV");
		suggestionDiv.setAttribute("id", this.id + "autocomplete-list");
		suggestionDiv.setAttribute("class", "autocomplete-items");
		this.parentNode.appendChild(suggestionDiv);

		for (let i = 0; i < suggestions.length; i++) {
			// check if the item starts with the same letters as the text field value
			if (suggestions[i].substr(0, val.length).toUpperCase() === val.toUpperCase()) {
				let suggestion = document.createElement("DIV");
				suggestion.innerHTML = "<strong>" + suggestions[i].substr(0, val.length) + "</strong>";
				suggestion.innerHTML += suggestions[i].substr(val.length);

				switch (type) {
					case 'lector':
						/* Lector - add lecotr after click */
						suggestion.addEventListener("click", function () {
							const xhttp = new XMLHttpRequest();
							xhttp.onreadystatechange = function () {
								if (this.readyState === 4 && this.status === 200)
									refresh_lector_list(unit_id);
							};
							xhttp.open("GET", "ajax/unit-lector-insert.php?unitID=" + unit_id + "&lectorID=" + suggestions_ids[i], true);
							xhttp.send();
						});
						break;
					case 'unit':
						/* Unit - add unit after click */
						suggestion.addEventListener("click", function () {
							const xhttp = new XMLHttpRequest();
							xhttp.onreadystatechange = function () {
								if (this.readyState === 4 && this.status === 200)
									refresh_unit_list(unit_id);
									//window.alert("addin unit_unit\n" + this.responseText);
							};
							xhttp.open("GET", "ajax/unit-unit-insert.php?parentID=" + unit_id + "&childID=" + suggestions_ids[i], true); //todo
							xhttp.send();
						});
						break;
					default:
						/* Client - offer buttoons ADD / INVITE */
						let inputElement = document.createElement('input');
						inputElement.type = "button";
						inputElement.value = "ADD";
						inputElement.classList.add("suggestion-button", "suggestion-button-add");
						inputElement.addEventListener('click', function () {
							unit_add_client(suggestions_ids[i], unit_id, 'manual');
						});
						suggestion.appendChild(inputElement);

						let inputElement2 = document.createElement('input');
						inputElement2.type = "button";
						inputElement2.value = "INVITE";
						inputElement2.classList.add("suggestion-button", "suggestion-button-invite");
						inputElement2.addEventListener('click', function () {
							unit_add_client(suggestions_ids[i], unit_id, 'invite');
						});
						suggestion.appendChild(inputElement2);
				}

				suggestion.addEventListener("click", function() {
					closeAllLists();
				});
				suggestionDiv.appendChild(suggestion);
			}
		}
	});

	input.addEventListener("keydown", function(e) {
		let x = document.getElementById(this.id + "autocomplete-list");
		if (x) x = x.getElementsByTagName("div");
		if (e.keyCode === 40) { // down key
			currentSuggestionFocus++;
			highlightActiveChoice(x);
		} else if (e.keyCode === 38) { // up key
			currentSuggestionFocus--;
			highlightActiveChoice(x);
		} else if (e.keyCode === 13) { // enter key
			e.preventDefault(); // do not submit
			if (currentSuggestionFocus > -1)
				if (x) x[currentSuggestionFocus].click(); // simulate click on activr suggestion
		}
	});

	function highlightActiveChoice(x) {
		if (!x) return false;
		for (let i = 0; i < x.length; i++)
			x[i].classList.remove("autocomplete-active");
		if (currentSuggestionFocus >= x.length) currentSuggestionFocus = 0;
		if (currentSuggestionFocus < 0) currentSuggestionFocus = (x.length - 1);
		x[currentSuggestionFocus].classList.add("autocomplete-active");
	}

	function closeAllLists(elmnt) {
		let x = document.getElementsByClassName("autocomplete-items");
		for (let i = 0; i < x.length; i++)
			if (elmnt !== x[i] && elmnt !== input)
				x[i].parentNode.removeChild(x[i]);
	}

	document.addEventListener("click", function (e) {
		closeAllLists(e.target);
	});
}
