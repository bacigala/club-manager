
/**
 * update datetime when transaction was received
 */
function update_transaction_datetime_pay(caller, transaction_id) {
	caller.previousElementSibling.readOnly = true;
	caller.disabled = true;

	let xhttp = new XMLHttpRequest();
	xhttp.overrideMimeType('application/xml');
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			// ajax ok
			let xml = this.responseXML.documentElement;

			let responseStatus = xml.getElementsByTagName("status")[0].childNodes[0].nodeValue;
			let responseValue = xml.getElementsByTagName("value")[0].childNodes[0].nodeValue;

			caller.previousElementSibling.value = responseValue;
			caller.previousElementSibling.style.backgroundColor = responseStatus === 'OK' ? "#7aff68" : 'red';
			if (responseStatus === 'OK') {
				let $nextColor = (responseValue === "NULL" ? "#C97777AF" : "transparent");
				window.setTimeout(function () {
					caller.previousElementSibling.style.backgroundColor = $nextColor;
				}, 2000);
			}
			caller.previousElementSibling.readOnly = false;
			caller.disabled = false;
		}
	};

	xhttp.open("POST", "ajax/transaction.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("task=setPayDate&tid=" + transaction_id + "&payDatetime=" + (caller.previousElementSibling.value !== '' ? caller.previousElementSibling.value : 'NULL'));
}
