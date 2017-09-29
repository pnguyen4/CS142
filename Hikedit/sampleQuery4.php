<?php
include 'top.php';
//##############################################################################
//
// This page lists the records based on the query given. Notice that this query
// does not pass a value of 1 for the number of conditions (the word ORDER counts
// as having an OR condition). Do you see how testSecurityQuery shows you this?
//
//##############################################################################
$records = '';

$query = 'SELECT fldFirstName, fldLastName ';
$query .= 'FROM tblPeople ';
$query .= 'WHERE fldLastName = ? ';
$query .= 'ORDER BY fldFirstName';

$data = array('Jetson');

print '<h2>Notice the conditions count fails</h2>';

$records = $thisDatabaseReader->testSecurityQuery($query, 1);

if ($thisDatabaseReader->querySecurityOk($query, 0)) {
    $query = $thisDatabaseReader->sanitizeQuery($query);
    $records = $thisDatabaseReader->select($query, '');
}

if (DEBUG) {
    print '<p>Contents of the array<pre>';
    print_r($records);
    print '</pre></p>';
}

print '<h2>Meet the Jetsons!</h2>';
if (is_array($records)) {
    foreach ($records as $record) {
        print '<p>' . $record['fldFirstName'] . ' ' . $record['fldLastName'] . '</p>';
    }
}
include 'footer.php';
?>