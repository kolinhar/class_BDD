<html>
    <head>
        <meta charset="UTF-8">
        <style>
            body{
                background-color: #000;
                color: #fff;
            }
        </style>
    </head>
    <body>
<?php
/**
 * Created by PhpStorm.
 * User: juanes
 * Date: 19/07/14
 * Time: 22:31
 */
include "class_BDD.php";

echo "<p>instanciation sans paramètre:</p>";
$oBDD = new BDD;
$oBDD->DataConnection("root", "","mysql:host=localhost;dbname=freeh_utopia");
$oBDD->RqtSelect("select * from feuilles order by id_perso asc", []);
$oBDD->NextLine();
?>
        <pre>
<?php
echo "1er ID trouvé : ".$oBDD->Valeur("id_perso");
?>
        </pre>
<?php
echo "<p>instanciation avec paramètre:</p>";
$oBDD2 = new BDD("root", "","mysql:host=localhost;dbname=freeh_utopia");
$oBDD2->RqtSelect("select * from feuilles order by id_perso asc", []);
$oBDD2->NextLine();
?>
        <pre>
<?php
echo "1er ID trouvé : ".$oBDD2->Valeur("id_perso");
?>
        </pre>
    </body>
</html>