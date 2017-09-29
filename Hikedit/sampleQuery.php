<?php
include 'top.php';
//##############################################################################
//
// This page lists the records based on the query given
// 
//##############################################################################
$records = '';

$query = 'SELECT fldFirstName, fldLastName FROM tblPeople';

// NOTE: The full method call would be:
//           $thisDatabaseReader->querySecurityOk($query, 0, 0, 0, 0, 0)
if ($thisDatabaseReader->querySecurityOk($query, 0)) {
    $query = $thisDatabaseReader->sanitizeQuery($query);
    $records = $thisDatabaseReader->select($query, '');
    
}

if (DEBUG) {
    print '<p>Contents of the array<pre>';
    print_r($records);
    print '</pre></p>';
}

print '<h2 class="alternateRows">Meet the Jetsons!</h2>';
if (is_array($records)) {
    foreach ($records as $record) {
        print '<p>' . $record['fldFirstName'] . ' ' . $record['fldLastName'] . '</p>';
    }
}
include 'footer.php';
?>