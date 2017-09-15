<?php

include 'top.php';
//##############################################################################
//
// This page is designed to show how to insert a record. It does not actually
// insert a record since that line is commented out. It does show the results
// the testquery method so you can see what that looks like.
//
//##############################################################################
// This example does not have a form so these two lines are just to put values
// in manually for this example.
$_POST['txtFirstName'] = 'Cosmo G.';
$_POST['txtLastName'] = 'Spacely';

// create array to hold values for query
$data = array();

// retreive values from form and store in $data array
$firstName = htmlentities($_POST['txtFirstName'], ENT_QUOTES, 'UTF-8');
$data[] = $firstName;

$lastName = htmlentities($_POST['txtLastName'], ENT_QUOTES, 'UTF-8');
$data[] = $lastName;

$query = 'INSERT INTO tblPeople SET ';
$query .= 'fldFirstName = ?, ';
$query .= 'fldLastName = ?';

// demostration of test query method which returns nothing but displays information.
print '<h2>TEST Query method</h2>';
$records = $thisDatabaseWriter->testSecurityQuery($query, 0);

// lets print out the data array so we can see what values would replace the ?
print '<p>Contents of the array<pre>';
print_r($data);
print '</pre></p>';

// this will insert the data, since i dont want Mr. Spacely entered into my table
// again i commented this line out. $records will always be false because of that.
print '<h2>Insert method</h2>';
if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
    $query = $thisDatabaseReader->sanitizeQuery($query);
    //$records = $thisDatabaseWriter->insert($query, $data);
}

if ($records) {
    print '<p>Record Saved</p>';
} else {
    print '<p>Record NOT Saved</p>';
}

include 'footer.php';
?>