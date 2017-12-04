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
?>

<div id="wrapper">

    <h1 id="greeter"> Welcome </h1>
    <div id="index1">
        <img id="indextea" src="images/camellia-sinensis.jpg">
        <h2> Camellia Sinensis </h2>
        <p><i>kəˈmiːljə /sinensis/</i></p>
        <p>The history of this miraculous plant has its origins in China, where
        containers of it have been found in tombs dating all the way back to the Han
        dynasty (206BC - 220AD). Camellia, commonly referred to as "the tea plant"
        was introduced to Japan in 9th century by priests and envoys. Europeans
        eventually became exposed to this ancient brew by Portugese and Dutch merchants
        in the 16th century. Since then tea been part of a rich history involving
        smuggling, trading, wars, and revolutions. It could be found in the echelons of
        western high society all the way down to the eastern countryside. Today tea brewing
        is a beloved art and pastime enjoyed by many, and it is here to stay.
        </p>
    </div>
    <div id="index2">
        <img id="indexcup" src="images/teacup.gif">
        <h2 > Numerous Benefits </h2>
        <p >  Tranquility. Clarity. </p>
        <p>Our products will be the highlight of your day. The fine scent and delicious taste
        is only the tip of the iceberg! Did you know that tea is full of antioxidants and other
        healthy compounds such as L-theanine and Vitamin C? It is also incredibly hydrating
        despite the caffeine content. Humans have been drinking tea for thousands of years to
        lift their mood and revitalize their spirits. Enjoy with a couple of friends to create
        lasting memories, or drink alone during stressful times to help bring peace of mind.
    </div>
</div>

<?php
include 'footer.php';
?>
