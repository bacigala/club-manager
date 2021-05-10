
/**
 * Create new unit (BUTTON click)
 */
function create_unit(type) {
	let name = window.prompt("Názov:", "");
	if (name === null) return;

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



/*
 * GENERAL UNIT DETAILS & FUNCTIONS
 */

/**
 * Called by click on unit row in admin-unit-ovreview table.
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
 * Called by "save" button next to detail.
 */
function update_unit_detail(caller, unit_id) {
	caller.previousElementSibling.readOnly = true;
	let xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
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


/*
 *	UNIT ACCORDION SUBSECTIONS - lectors / clients / units
 */
// called by clicking on subsection <tr> - renders subsection in target
function load_unit_subsection(caller, target, unit_id, type) {
	if (target.style.display === '') {
		target.style.display = 'none';
		caller.style.backgroundColor = '';
		caller.style.color = '';
	} else {
		target.style.display = ''; // show
		caller.style.backgroundColor = "mediumseagreen";
		caller.style.color = 'white';
		refresh_unit_subsection(unit_id, type);
	}
}

// insert subsection content
function refresh_unit_subsection(unit_id, type) {
	let target = document.getElementsByClassName("unit" + unit_id + type + "ListContainer");
	Array.prototype.forEach.call(target, function (t) {
		t.innerHTML = "Loading...";
	});

	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			let that = this;
			Array.prototype.forEach.call(target, function (t) {
				t.innerHTML = that.responseText;
				setup_subsection_autocomplete(unit_id, type);
			});
		}
	};
	xhttp.open("GET", "ajax/unit-subsection-load.php?unitID=" + unit_id + "&type=" + type, true);
	xhttp.send();
}

// retrieve and set suggestions for subsection content
function setup_subsection_autocomplete(unit_id, type) {
	let xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			// ajax ok
			if (this.responseText === '') return;

			// parse received XML
			let xml = this.responseXML.documentElement;

			let names = xml.getElementsByTagName("suggestion");
			let suggestion_text = [];
			for (let i = 0; i < names.length; i++)
				suggestion_text.push(names[i].childNodes[0].nodeValue);

			let ids = xml.getElementsByTagName("id");
			let suggestion_id = [];
			for (let i = 0; i < ids.length; i++)
				suggestion_id.push(ids[i].childNodes[0].nodeValue);

			let types = xml.getElementsByTagName("type");
			let suggestion_type = [];
			for (let i = 0; i < types.length; i++)
				suggestion_type.push(types[i].childNodes[0].nodeValue);


			// create autocomplete html for each suggestion
			let autocomplete_elements = document.getElementsByClassName(type +"Search" + unit_id);
			Array.prototype.forEach.call(autocomplete_elements, function(element) {
				autocomplete_setup(element, suggestion_text, suggestion_id, suggestion_type, unit_id, type);
			});
		}
	};
	xhttp.open("GET", "ajax/unit-suggestion-load.php?unitID=" + unit_id + "&type=" + type, true);
	xhttp.send();
}



/*
 * UNIT LECTORS
 */
// called e.g. on click in lector suggestions for unit
function insert_unit_lector(unit_id, lector_id, callback) {
	const xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '')
				window.alert(this.responseText);
			callback(this.responseText === '');
		}
	};
	xhttp.open("POST", "ajax/unit-lector-insert.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("unitID=" + unit_id + "&lectorID=" + lector_id);
}

// called on "save" button click in unit-lector subsection -> update "is_editor"
function update_unit_lector(caller, unit_id, lector_id) {
	caller.disabled = true;
	caller.value = "WAIT...";
	let is_editor = caller.previousElementSibling.checked;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			// ajax ok
			refresh_unit_subsection(unit_id, 'lector');
		}
	};

	xhttp.open("POST", "ajax/unit-lector-update.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("unitID="+unit_id+"&lectorID="+lector_id+"&is_editor="+is_editor);
}

// called on "delete" button click in unit-lector subsection -> delete unit-lecotr association
function delete_unit_lector(caller, unit_id, lector_id) {
	caller.disabled = true;
	caller.value = "WAIT...";
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText === 'true') {
				caller.disabled = false;
				refresh_unit_subsection(unit_id, 'lector');
				caller.value = "Ulozit";
			} else {
				window.alert(this.responseText);
			}
		}
	};
	xhttp.open("POST", "ajax/unit-lector-delete.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("unitID=" + unit_id + "&lectorID=" + lector_id);
}



/*
 * UNIT CLIENTS
 */

// button click in unit-client subsection
function update_unit_client_status(caller, unit_id, client_id, desired_status) {
	caller.disabled = true;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') {
				window.alert("ERROR\n" + this.responseText);
			}
			// ajax ok
			caller.disabled = false;
			refresh_unit_subsection(unit_id, 'client');
		}
	};
	xhttp.open("GET", "ajax/unit-client-update.php?unitID="+unit_id+"&clientID="+client_id+"&status="+desired_status, true);
	xhttp.send();
}

// button click in CLIENT SUGGESTION LIST (client add/invite)
function unit_add_client(entity_id, unit_id, status = 'manual', entity_type = 'client') {
	// ajax query to add user to unit
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') { // error
				window.alert("ERROR\n" + this.responseText);
				return;
			}
			// ajax ok
			refresh_unit_subsection(unit_id, 'client');
		}
	};
	xhttp.open("GET", "ajax/unit-client-insert.php?unitID="+unit_id+"&entityId="+entity_id+"&entityType="+entity_type+"&status="+status, true);
	xhttp.send();
}



/*
 * UNIT UNITS (EVENTS OF COURSES / OCCURENCES OF EVENTS)
 */

/**
 *	Create ocurrence (BUTTON click)
 */
function create_ocurrence(caller, unit_id) {
	caller.enabled = false;
	event.stopPropagation();
	let name = window.prompt("Názov:", "Nový výskyt udalosti");
	if (name === null) return;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '')
				window.alert("CHYBA!\nVýkyt sa nepodarilo vytvoriť.\n" + this.responseText);
			caller.enabled = true;
			refresh_unit_subsection(unit_id, 'unit')
		}
	};
	xhttp.open("GET", "ajax/ocurrence-create.php?unitID="+unit_id+"&name="+name, true);
	xhttp.send();
}

// called e.g. on click in unit suggestions for course
function insert_unit_unit(parent_id, child_id, callback) {
	const xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '')
				window.alert(this.responseText);
			callback(this.responseText === '');
		}
	};
	xhttp.open("GET", "ajax/unit-unit-insert.php?parentID=" + parent_id + "&childID=" + child_id, true);
	xhttp.send();
}

// called on button click in unit accordeon -  delete ocurrence of event OR detach event from course
function unit_detach(caller, parent_id, child_id) {
	caller.enabled = false;
	event.stopPropagation();
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '')
				window.alert("ERROR\n" + this.responseText);
			caller.enabled = true;
			refresh_unit_subsection(parent_id, 'unit')
		}
	};
	xhttp.open("POST", "ajax/unit-unit-delete.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("parentID=" + parent_id + "&childID=" + child_id);
}



/*
 * AUTOCOMPLETE COMPONENT
 */

// attach autocomplete div to input field
function autocomplete_setup(input, suggestions, suggestions_ids, suggestion_type, unit_id, type = 'client') {
	let currentSuggestionFocus = -1;
	// prepare <div>s for each suggestion
	let preparedSuggestions = [];

	let onInsertDone = function (result) {
		if (result) refresh_unit_subsection(unit_id, type);
	}

	for (let i = 0; i < suggestions.length; i++) {
		let suggestion = document.createElement("DIV");
		let suggestion_name = document.createElement("span");
		suggestion.appendChild(suggestion_name);

		switch (type) {
			case 'lector':
				/* Lector - add lecotr after click */
				suggestion.addEventListener("click", function() {
					insert_unit_lector(unit_id, suggestions_ids[i], onInsertDone);
				});
				break;
			case 'unit':
				/* Unit - add unit after click */
				suggestion.addEventListener("click", function() {
					insert_unit_unit(unit_id, suggestions_ids[i], onInsertDone);
				});
				break;
			case 'client':
				/* Client & Unit - offer buttoons ADD / INVITE */
				let inputElement = document.createElement('input');
				inputElement.type = "button";
				inputElement.value = "ADD";
				inputElement.classList.add("suggestion-button", "suggestion-button-add");
				let addFunction = function () { unit_add_client(suggestions_ids[i], unit_id, 'manual', suggestion_type[i]); };
				inputElement.addEventListener('click', addFunction);
				suggestion.appendChild(inputElement);

				let inputElement2 = document.createElement('input');
				inputElement2.type = "button";
				inputElement2.value = "INVITE";
				inputElement2.classList.add("suggestion-button", "suggestion-button-invite");
				addFunction = function () { unit_add_client(suggestions_ids[i], unit_id, 'invite',suggestion_type[i]); };
				inputElement2.addEventListener('click', addFunction);
				suggestion.appendChild(inputElement2);
		}

		suggestion.addEventListener("click", function() {
			closeAllLists();
		});
		preparedSuggestions[i] = suggestion;
	}

	// refresh shown suggestions on input change
	input.addEventListener("input", function() {
		closeAllLists();
		let val = this.value;
		if (!val) return false;
		currentSuggestionFocus = -1;
		let suggestionDiv = document.createElement("DIV");
		suggestionDiv.setAttribute("id", this.id + "autocomplete-list");
		suggestionDiv.setAttribute("class", "autocomplete-items");
		this.parentNode.appendChild(suggestionDiv);

		for (let i = 0; i < suggestions.length; i++) {
			let index = -1;
			if ((index = suggestions[i].toUpperCase().indexOf(val.toUpperCase())) >= 0) {
				let suggestion = preparedSuggestions[i];
				let suggestionName = suggestion.getElementsByTagName("span")[0];
				suggestionName.innerHTML = suggestions[i].substr(0, index)
				suggestionName.innerHTML += "<strong>" + suggestions[i].substr(index, val.length) + "</strong>";
				suggestionName.innerHTML += suggestions[i].substr(index + val.length);
				suggestionDiv.appendChild(suggestion);
			}
		}
	});

	// suggestions traverse
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
			e.preventDefault(); // do not submit input form
			if (currentSuggestionFocus > -1)
				if (x) x[currentSuggestionFocus].click(); // simulate click on active suggestion -> add
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
