<?php
include 'top.php';
//##############################################################################
//
// This page lists the records based on the query given
//
//##############################################################################
//$records = '';

//$query = 'SELECT * FROM tblTrails';

// NOTE: The full method call would be:
//           $thisDatabaseReader->querySecurityOk($query, 0, 0, 0, 0, 0)
//if ($thisDatabaseReader->querySecurityOk($query, 0)) {
//    $query = $thisDatabaseReader->sanitizeQuery($query);
//    $records = $thisDatabaseReader->select($query, '');
//}

if (DEBUG) {
    print '<p>Contents of the array<pre>';
    print_r($records);
    print '</pre></p>';
}

include 'footer.php';
?>
