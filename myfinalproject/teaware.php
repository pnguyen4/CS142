<?php
include 'top.php';

if (DEBUG) {
    print '<p>Contents of the array<pre>';
    print_r($records);
    print '</pre></p>';
}
?>
<div id="wrapper">
<p style="padding: 2em;">We are restocking! Check back soon!</p>
</div>
<?php
include 'footer.php';
?>
