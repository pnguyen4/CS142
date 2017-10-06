<?php
include 'top.php';
//##############################################################################
//
// This page lists the records based on the query given
//
//##############################################################################
$records = '';

$query = 'SELECT * FROM tblTrails';

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

print '<h2 class="alternateRows">List tblTrails data:</h2>';
print "<h3>fldTrailName | fldTotalDistance | fldHikingTime | fldVerticalRise | fldRating</h3>";
if (is_array($records)) {
    foreach ($records as $record) {
        //print '<p>' . $record['fldFirstName'] . ' ' . $record['fldLastName'] . '</p>';
		print '<p>' . $record['fldTrailName']. " " . $record['fldTotalDistance']
		. " " . $record['fldHikingTime'] . " " . $record['fldVerticalRise'] . " " .
		$record['fldRating'] . '</p>';
    }
}
include 'footer.php';
?>
