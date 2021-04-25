
// UNIT DETAILS

/**
 * Called onClick in admin-courses-ovreview
 * Ajax call to render unit details in next table row.
 */
function load_unit_details(caller, unit_id) {
	let display = caller.nextSibling.style.display;
	if (display === '') {
		caller.nextSibling.style.display = 'none';
		caller.style.backgroundColor = '';
	} else {
		caller.style.backgroundColor = "orange";
		caller.nextSibling.style.display = '';
		caller.nextSibling.firstChild.firstChild.innerHTML = "WAIT...";
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState === 4 && this.status === 200) {
				// ajax ok
				caller.nextSibling.firstChild.firstChild.innerHTML = this.responseText;
			}
		};
		xhttp.open("GET", "ajax/load_unit_details.php?q="+unit_id, true);
		xhttp.send();
	}
}

function update_unit_detail(caller, unit_id) {
	caller.previousElementSibling.readOnly = true;
	caller.style.backgroundColor = "orange";
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
			caller.style.backgroundColor = responseStatus === 'OK' ? "green" : 'red';
			caller.previousElementSibling.readOnly = false;
		}
	};
	xhttp.open("GET", "ajax2.php?unitID="+unit_id+"&property="+caller.previousElementSibling.name+"&value="+caller.previousElementSibling.value, true);
	xhttp.send();
}


// UNIT LECTORS

function load_unit_lectors(caller, unit_id) {
	const display = caller.nextSibling.style.display;
	if (display === '') {
		caller.nextSibling.style.display = 'none';
		caller.style.backgroundColor = '';
	} else {
		caller.style.backgroundColor = "blue";
		caller.nextSibling.style.display = ''; // show nextSibling
		caller.nextSibling.firstChild.firstChild.innerHTML = "WAIT...";
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				// ajax ok
				caller.nextSibling.firstChild.firstChild.innerHTML = this.responseText;
				setup_lector_autocomplete(unit_id);
			}
		};
		xhttp.open("GET", "ajax/load_unit_lectors.php?unitID=" + unit_id, true);
		xhttp.send();
	}
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
				autocomplete(element, suggestion, suggestion_id, unit_id);
			});
		}
	};
	xhttp.open("GET", "ajax/get_lector_suggestion_for_unit.php?unitID="+unit_id, true);
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
			caller.previousElementSibling.checked = this.responseText === true ? 'true' : '';
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
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== 'true') {
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
	let display = caller.nextSibling.style.display;
	if (display === '') {
		caller.nextSibling.style.display = 'none';
		caller.style.backgroundColor = '';
	} else {
		caller.style.backgroundColor = "blue";
		caller.nextSibling.style.display = ''; // show nextSibling
		caller.nextSibling.firstChild.firstChild.innerHTML = "WAIT...";
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState === 4 && this.status === 200) {
				caller.nextSibling.firstChild.firstChild.innerHTML = this.responseText;
				setup_client_autocomplete(unit_id);
			}
		};
		xhttp.open("GET", "ajax/load_unit_clients.php?unitID="+unit_id, true);
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
	xhttp.open("GET", "ajax/load_unit_clients.php?unitID="+unit_id, true);
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


			// 'activate' autocomplete for each lector-search-input of this unit
			let autocomplete_elements = document.getElementsByClassName("clientSearch" + unit_id);
			Array.prototype.forEach.call(autocomplete_elements, function(element) {
				autocomplete_client(element, suggestion, suggestion_id, unit_id);
			});
		}
	};
	xhttp.open("GET", "ajax/get_client_suggestion_for_unit.php?unitID="+unit_id, true);
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
	xhttp.open("GET", "ajax/update_unit_client_status.php?unitID="+unit_id+"&clientID="+client_id+"&status="+desired_status, true);
	xhttp.send();
}





function autocomplete(inp, arr, arr_id, unit_id) {
	/*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
	let currentFocus;
	/*execute a function when someone writes in the text field:*/
	inp.addEventListener("input", function(e) {
		var a, b, i, val = this.value;
		/*close any already open lists of autocompleted values*/
		closeAllLists();
		if (!val) { return false;}
		currentFocus = -1;
		/*create a DIV element that will contain the items (values):*/
		a = document.createElement("DIV");
		a.setAttribute("id", this.id + "autocomplete-list");
		a.setAttribute("class", "autocomplete-items");
		/*append the DIV element as a child of the autocomplete container:*/
		this.parentNode.appendChild(a);
		/*for each item in the array...*/
		for (i = 0; i < arr.length; i++) {
			/*check if the item starts with the same letters as the text field value:*/
			if (arr[i].substr(0, val.length).toUpperCase() === val.toUpperCase()) {
				/*create a DIV element for each matching element:*/
				b = document.createElement("DIV");
				/*make the matching letters bold:*/
				b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
				b.innerHTML += arr[i].substr(val.length);
				/*insert a input field that will hold the current array item's value:*/
				b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
				b.innerHTML += "<input type='hidden' value='" + arr_id[i] + "'>";
				/*execute a function when someone clicks on the item value (DIV element):*/
				b.addEventListener("click", function(e) {
					var suggestion = this.getElementsByTagName("input")[0].value;
					var suggestion_id = this.getElementsByTagName("input")[1].value;
					inp.value = suggestion;
					closeAllLists();
					arr.splice(i,1); // do not suggest this anymore
					// TRY TO ADD LECTOR TO UNIT
					const xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange = function() {
						if (this.readyState === 4 && this.status === 200) {
							//caller.previousElementSibling.checked = this.responseText == true ? 'true' : '';
							//caller.disabled = false;
							/// todo: update gui
						}
					};
					xhttp.open("GET", "ajax/add_lector_to_unit.php?unitID="+unit_id+"&lectorID="+suggestion_id, true);
					xhttp.send();
				});
				a.appendChild(b);
			}
		}
	});
	/*execute a function presses a key on the keyboard:*/
	inp.addEventListener("keydown", function(e) {
		var x = document.getElementById(this.id + "autocomplete-list");
		if (x) x = x.getElementsByTagName("div");
		if (e.keyCode === 40) {
			/*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
			currentFocus++;
			/*and and make the current item more visible:*/
			addActive(x);
		} else if (e.keyCode === 38) { //up
			/*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
			currentFocus--;
			/*and and make the current item more visible:*/
			addActive(x);
		} else if (e.keyCode === 13) {
			/*If the ENTER key is pressed, prevent the form from being submitted,*/
			e.preventDefault();
			if (currentFocus > -1) {
				/*and simulate a click on the "active" item:*/
				if (x) x[currentFocus].click();
			}
		}
	});
	function addActive(x) {
		/*a function to classify an item as "active":*/
		if (!x) return false;
		/*start by removing the "active" class on all items:*/
		removeActive(x);
		if (currentFocus >= x.length) currentFocus = 0;
		if (currentFocus < 0) currentFocus = (x.length - 1);
		/*add class "autocomplete-active":*/
		x[currentFocus].classList.add("autocomplete-active");
	}
	function removeActive(x) {
		/*a function to remove the "active" class from all autocomplete items:*/
		for (var i = 0; i < x.length; i++) {
			x[i].classList.remove("autocomplete-active");
		}
	}
	function closeAllLists(elmnt) {
		/*close all autocomplete lists in the document,
        except the one passed as an argument:*/
		var x = document.getElementsByClassName("autocomplete-items");
		for (var i = 0; i < x.length; i++) {
			if (elmnt !== x[i] && elmnt !== inp) {
				x[i].parentNode.removeChild(x[i]);
			}
		}
	}
	/*execute a function when someone clicks in the document:*/
	document.addEventListener("click", function (e) {
		closeAllLists(e.target);
	});
}






// attach autocomplete div to input field
function autocomplete_client(input, suggestions, suggestions_ids, unit_id) {
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
			/*check if the item starts with the same letters as the text field value:*/
			if (suggestions[i].substr(0, val.length).toUpperCase() === val.toUpperCase()) {
				let suggestion = document.createElement("DIV");
				suggestion.innerHTML = "<strong>" + suggestions[i].substr(0, val.length) + "</strong>";
				suggestion.innerHTML += suggestions[i].substr(val.length);

				let inputElement = document.createElement('input');
				inputElement.type = "button";
				inputElement.value = "ADD";
				inputElement.addEventListener('click', function(){
					unit_add_client(suggestions_ids[i], unit_id, 'manual');
				});
				suggestion.appendChild(inputElement);

				let inputElement2 = document.createElement('input');
				inputElement2.type = "button";
				inputElement2.value = "INVITE";
				inputElement2.addEventListener('click', function(){
					unit_add_client(suggestions_ids[i], unit_id, 'invite');
				});
				suggestion.appendChild(inputElement2);

				suggestion.addEventListener("click", function(e) {
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
			//if (currentSuggestionFocus > -1)
			//if (x) x[currentSuggestionFocus].click(); // simulate click on activr suggestion
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
	xhttp.open("GET", "ajax/unit_add_client.php?unitID="+unit_id+"&clientID="+client_id+"&status="+status, true);
	xhttp.send();
}

// refresh client list of given unit
function refresh_client_list(unit_id) {
	let target = document.getElementsByClassName("unit" + unit_id + "clientListContainer");
	Array.prototype.forEach.call(target, function (t) {
		insert_unit_clients_table(unit_id, t);
	});
}
