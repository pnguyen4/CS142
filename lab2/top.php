<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Phillip, DB Example</title>
        <meta charset="utf-8">
        <meta name="author" content="Robert Erickson">
        <meta name="description" content="Bob, Thank you for the example">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
        <![endif]-->

        <link rel="stylesheet" href="css/base.css" type="text/css" media="screen">

        <?php
        // %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
        //
        // inlcude all libraries. 
        // 
        // %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
        print '<!-- begin including libraries -->';
        
        include 'lib/constants.php';

        include LIB_PATH . '/Connect-With-Database.php';

        print '<!-- libraries complete-->';
        ?>	

    </head>

    <!-- **********************     Body section      ********************** -->
    <?php
    print '<body id="' . $PATH_PARTS['filename'] . '">';
    include 'header.php';
    include 'nav.php';
    ?>
