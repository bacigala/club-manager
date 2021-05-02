
/**
 * toogle displayed payment detail in payment-modify form (WAIT / DONE)
 */
function payment_wait() {
	document.getElementById("payment-wait-detail").style.display = 'block';
	document.getElementById("payment-record-detail").style.display = 'none';
}

function payement_record() {
	document.getElementById("payment-wait-detail").style.display = 'none';
	document.getElementById("payment-record-detail").style.display = 'block';
}
