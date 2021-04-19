<?php

function input_date_format($date_string) {
	$date = date_create($date_string);
	return date_format($date, "Y-m-d") . 'T' . date_format($date, "H:i");
}

	session_start();
	include('db.php');
	include('functions.php');

	$id = $_REQUEST["unitID"];
	$property = $_REQUEST["property"];
	$value = $_REQUEST["value"];

 $query = "UPDATE unit SET $property = '$value' WHERE id=$id";
		if (!($result = $mysqli->query($query))) {
			//echo 'nevyslo to';
		} else {
			//echo $value;
		}
		
		
// <form action=""> 
  // <select name="customers" onchange="showCustomer(this.value)">
    // <option value="">Select a customer:</option>
    // <option value="ALFKI">Alfreds Futterkiste</option>
    // <option value="NORTS ">North/South</option>
    // <option value="WOLZA">Wolski Zajazd</option>
  // </select>
// </form>


if ($property == 'start_datetime' || $property == 'end_datetime') {
	$value = input_date_format($value);
}

	$dom = new DOMDocument();
		$dom->encoding = 'utf-8';
		$dom->xmlVersion = '1.0';
		$root = $dom->createElement('DBtransaction');
	$child_node_result_status = $dom->createElement('status', 'OK');
		$root->appendChild($child_node_result_status);
	$child_node_result_message = $dom->createElement('message', 'V+Setko oukej');
		$root->appendChild($child_node_result_message);
	$child_node_new_value = $dom->createElement('value', $value);
		$root->appendChild($child_node_new_value);
	$dom->appendChild($root);
	
	echo $dom->saveXml();
?>