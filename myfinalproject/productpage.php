<?php
include 'top.php';

if ($debug) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}

if (isset($_GET["Id"])) {
    $productId = (int) htmlentities($_GET["Id"], ENT_QUOTES, "UTF-8");
    $query = "SELECT * FROM tblProducts WHERE pmkProductId = ?";

    $data = array($productId);
    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $record = $thisDatabaseReader->select($query, $data);
    }

    $productName = $record[0]["fldProductName"];
    $price = $record[0]["fldPrice"];
    $instock = $record[0]["fldInStock"];
    $description = $record[0]["fldDescription"];
    $image = $record[0]["fldImgUrl"];

    print('
    <div id="wrapper">
        <div id="index3">
        <img id="productimg" src="'.$image.'">
        <h1 id="productname">'.$productName.'</h1>
        <h2>Price: $'.$price.'</h2>
        <h2>In Stock: '.$instock.'</h2>
        </div>
        <div id="index2">
        <p id="description">'.$description.'</p>
        </div>
    <div><!--end wrapper-->
    ');
} else {
    print '<p>This is not the page you are looking for.</p>';
}
?>


<?php
include 'footer.php';
?>
