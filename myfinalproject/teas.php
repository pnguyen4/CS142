<?php
include 'top.php';

if ($debug){  // later you can uncomment the if statement
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}
?>

<div id="wrapper">
    <div id="main">
    <?php
        $records = '';
        $query = '';
    if (isset($_POST["blacktea"])) {
        $query = 'SELECT * FROM tblProducts WHERE fnkCategoryId=2';
    }
    if (isset($_POST["greentea"])) {
        $query = 'SELECT * FROM tblProducts WHERE fnkCategoryId=3';
    }
        if ($thisDatabaseReader->querySecurityOk($query)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $records = $thisDatabaseReader->select($query, '');
        }
    if ($query != '') {
        foreach ($records as $record) {
            print "<figure>";
            print '<img class="teas" src='.$record['fldImgUrl'].' alt="'.
                $record['fldProductName'].'">';
            print '<figcaption><a href="productpage.php?Id='.$record['pmkProductId']
            .'">'.$record['fldProductName'].' - $'.$record['fldPrice'].'</a></figcaption>';
            $description = $record['fldDescription'];
            print "</figure>";
        }
    }
    ?>

    </div> <!--end main-->

    <div id="padding">
    </div> <!-- end padding, this one is for css reasons only-->

    <div id="sidebar">
    <form action="<?php print $phpSelf; ?>" id="frmSidebar" method="post">
    <ul>
        <li <?php if(isset($_POST["whitetea"])) print 'class ="activesidebar"'; ?>>
        <input class="sidebarbutton" type="submit" name="whitetea" value="white tea"/>
        </li>
        <li <?php if(isset($_POST["yellowtea"])) print 'class="activesidebar"'; ?>>
            <input class="sidebarbutton" type="submit" name="yellowtea" value="yellow tea"/>
        </li>
        <li <?php if(isset($_POST["greentea"])) print 'class="activesidebar"'; ?>>
            <input class="sidebarbutton" type="submit" name="greentea" value="green tea"/>
        </li>
        <li <?php if(isset($_POST["oolongtea"])) print 'class="activesidebar"'; ?>>
            <input class="sidebarbutton" type="submit" name="oolongtea" value="oolong tea"/>
        </li>
        <li <?php if(isset($_POST["blacktea"])) print 'class="activesidebar"'; ?>>
            <input class="sidebarbutton" type="submit" name="blacktea" value="black tea"/>
        </li>
        <li <?php if(isset($_POST["puerhtea"])) print 'class="activesidebar"'; ?>>
            <input class="sidebarbutton" type="submit" name="puerhtea" value="puerh tea"/>
        </li>
    </ul>
    </form>
    </div> <!--end sidebar-->
</div><!--end wrapper-->

<?php
include 'footer.php';
?>
