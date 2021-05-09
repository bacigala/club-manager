
/**
 * CLIENT: PAYMENT GROU OPERATIONS
 */

// button: select all
function select_all() {
	let checkboxes = document.getElementsByClassName("payment_checkbox");
	for (let i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = true;
	}
}

// highlight selected transaction
function highlight_transaction(transaction_name) {
	let allTransactions = document.getElementsByClassName("transaction");
	for (let i = 0; i < allTransactions.length; i++) {
		allTransactions[i].style.backgroundColor = '';
	}
	let targetTransactions = document.getElementsByClassName("transaction-" + transaction_name);
	for (let i = 0; i < targetTransactions.length; i++) {
		targetTransactions[i].style.backgroundColor = 'palegreen';
	}
}

// button: create transaction - creates transaction for selected items
function create_transaction() {
	let selectedPayments = [];

	let checkboxes = document.getElementsByClassName("payment_checkbox");
	for (let i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) selectedPayments.push(checkboxes[i].id);
	}

	if (selectedPayments.length <= 0 ) {
		window.alert("Neboli zvolené žiadne platby.");
		return;
	}

	// ajax - create transaction with selected payments
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') {
				window.alert("ERROR\nAkciu sa nepodarilo vykonat\n" + this.responseText)
			} else {
				// reload page to see results
				location.reload();
			}
		}
	};
	xhttp.open("POST", "ajax/transaction.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("task=create&payments=" + selectedPayments.toString());
}

// button: delete transaction (allow only if it was not paid yet)
function delete_transaction(caller, transaction_id) {
	caller.disabled = false;

	let message = "Pozor!\nTransakciu nerušte, ak ste už odoslali platbu s jej detailami."
					+ " Bez transakcie nebude možné priradiť platbu k Vášmu účtu."
					+ " \n\n Transakcia bude zrušená.";
	if (!window.confirm(message)) return;

	// ajax - create transaction with selected payments
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') {
				window.alert("ERROR\nAkciu sa nepodarilo vykonat\n" + this.responseText)
			} else {
				// reload page to see results
				caller.enabled = true;
				location.reload();
			}
		}
	};
	xhttp.open("POST", "ajax/transaction.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("task=delete&tid=" + transaction_id);
}

// button: BUY CREDIT
function buy_credit() {
	let amount = parseFloat(prompt("Vložte požadovanú hodnotu kreditu:", "100"));
	if (isNaN(amount)) {
		window.alert("No valid amount recognised.")
		return;
	}
	// round to 2 decimal
	amount = Math.round((amount + Number.EPSILON) * 100) / 100;

	// ajax - create transaction with selected payments
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') {
				window.alert("ERROR\nAkciu sa nepodarilo vykonat\n" + this.responseText)
			} else {
				// reload page to see results
				location.reload();
			}
		}
	};
	xhttp.open("POST", "ajax/payment.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("task=create&iid=0&amount=" + amount);
}

// button: delete payment (not yet paid credit payment)
function delete_payment(caller, payment_id) {
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') {
				window.alert("ERROR\nAkciu sa nepodarilo vykonat\n" + this.responseText)
			} else {
				// reload page to see results
				location.reload();
			}
		}
	};
	xhttp.open("POST", "ajax/payment.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("task=delete&pid=" + payment_id);
}

// button: pay payment by credit
function pay_by_credit(caller, payment_id) {
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText !== '') {
				window.alert("ERROR\nAkciu sa nepodarilo vykonat\n" + this.responseText)
			} else {
				// reload page to see results
				location.reload();
			}
		}
	};
	xhttp.open("POST", "ajax/payment.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("task=credit&pid=" + payment_id);
}

function group_pay_by_credit() {
	let checkboxes = document.getElementsByClassName("payment_checkbox");
	for (let i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) pay_by_credit(null, checkboxes[i].id);
	}
}