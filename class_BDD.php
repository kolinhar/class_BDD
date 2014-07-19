<?php
/**
 * FAIT TOUT PLANTER, COMMENTÉ EN ATTENDANT DE TROUVER LA SOLUTION
 * namespace GestBdd;
*/

/**
 * Class BDD
 * @package no_namespace
 * @notes CLASSE DE BASE POUR LES CONNEXIONS AVEC LA BASE DE DONNÉES
 */
class BDD
{
    private $debugMode = true;
    protected $LaRequete;
    protected $Parametres;
    protected $pdo;
    protected $Resultat;
    protected $login = "";
    protected $mdp = "";
    protected $StrConn = '';
    protected $Curseur;
    public $LastInsertId = 0;


    //PRÉPARE LA CONNEXION À LA BASE DE DONNÉES
    public function __construct()
    {
        if(func_num_args() == 3){
            /**
             * INFOS DE CONNEXION À LA BDD LOCAL
            $this->DataConnection("root", "", "mysql:host=localhost;dbname=freeh_utopia");
            */
            $this->DataConnection(func_get_arg(0), func_get_arg(1), func_get_arg(2));
        }
    }

    /**
     * @param $log
     * @param $pwd
     * @param $strconn
     */
    public function DataConnection($log, $pwd, $strconn)
    {
        try {
            $this->login = $log;
            $this->mdp = $pwd;
            $this->StrConn = $strconn;
            $this->pdo = new PDO($this->StrConn, $this->login, $this->mdp);

            //LES ERREURS GÉNÈRERONT DES EXCEPTIONS INTERCEPTABLES AVEC TRY/CATCH
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (Exception $e) {
            echo "<p>Erreur : Impossible de se connecter à la base de données.<br />".($this->debugMode==true ? "chaîne de connexion : ".$this->StrConn."<br />login : ".$this->login."<br />mot de passe : ".$this->mdp."<br />" : "").$e->getMessage()."</p>";
        }
    }

    //GÈRE LES REQUÊTES DE TYPE INSERT, UPDATE ET DELETE
    public function RqtIUD($rqte, $param=array())
    {
        // echo "<p>appel de RqtIUD</p>";
        $this->LaRequete = trim($rqte);
        $this->pdo->beginTransaction();

        if (count($param) > 0)
        {
            try
            {
                $this->Resultat = $this->pdo->prepare($this->LaRequete);

                try
                {
                    $this->Resultat->execute($param);
                }
                catch (Exception $e)
                {
                    $this->pdo->rollBack();
                    echo "<p>Erreur : problème à l'éxécution de la requête de modification.<br />".($this->debugMode==true ? $this->LaRequete."<br />".print_r($param) : "").$e->getMessage()."</p>";
                }

            }
            catch (Exception $e)
            {
                echo "<p>Erreur : problème avec la requête de modification.<br />".($this->debugMode==true ? $this->LaRequete."<br />" : "").$e->getMessage()."</p>";
            }
        }
        else
        {
            try
            {
                $this->Resultat = $this->pdo->exec($this->LaRequete);
            }
            catch (Exception $e)
            {
                echo "<p>Erreur : Impossible d'éxécuter la requête de modification.<br />".($this->debugMode==true ? $this->LaRequete."<br />" : "").$e->getMessage()."</p>";
            }
        }

        try
        {
            // echo "<p>Requête éffectuée</p>";
            $this->pdo->commit();

        }
        catch(Exception $e)
        {
            echo "Impossible de valider les informations<br>";
        }
    }


    //GÈRE LES REQUÊTES DE TYPE SELECT
    public function RqtSelect($rqte, $param=array())
    {
        $this->LaRequete = trim($rqte);
        $this->Parametres = $param;
        if (count($param) > 0)
        {
            try
            {
                $this->Resultat = $this->pdo->prepare($this->LaRequete);

                try
                {
                    $this->Resultat->execute($this->Parametres);
                }
                catch (Exception $e)
                {
                    echo "<p>Erreur : problème à l'éxécution de la requête de selection.<br />".($this->debugMode==true ? $this->LaRequete."<br />" : "").$e->getMessage()."</p>";
                }
            }
            catch (Exception $e)
            {
                echo "<p>Erreur : problème avec la requête de selection.<br />".($this->debugMode==true ? $this->LaRequete."<br />" : "").$e->getMessage()."</p>";
            }
        }
        else
        {
            try
            {
                $this->Resultat = $this->pdo->query($this->LaRequete);
            }
            catch (Exception $e)
            {
                echo "<p>Erreur : Impossible d'éxécuter la requête de selection.<br />".($this->debugMode==true ? $this->LaRequete."<br />" : "").$e->getMessage()."</p>";
            }
        }
    }

    //AFFICHE SOUS FORME DE TABLEAU LES RÉSULTATS DE LA REQUÊTE SELECT, SI RÉSULTATS IL Y A
    public function Affiche()
    {
        if ($this->LaRequete=="")
        {
            echo "Requête vide!<br />";
        }
        else
        {
            if ($this->debugMode==true)
            {
                echo "<p>mode débug</p>";
            }
            else
            {
                return;
            }

            if ($this->Resultat->fetchColumn()!="")
            {
                if (count($this->Parametres)>0)
                {
                    $this->Resultat = $this->pdo->prepare($this->LaRequete);
                    $this->Resultat->execute($this->Parametres);
                }
                else
                {
                    $this->Resultat = $this->pdo->query($this->LaRequete);
                }

                try
                {
                    $str  = "";
                    // echo "Liste : <b>$this->LaRequete</b>";
                    echo "\n<table border='1'>\n\t<tr class='liste'>\n";

                    try
                    {
                        // print_r();
                        $identifiant = 0;

                        foreach($this->Resultat->fetch(PDO::FETCH_ASSOC) as $key=>$val)
                        {
                            echo "\t\t<th class='liste'>".$key."</th>\n";
                            $str .= "\t\t<td>".nl2br(htmlspecialchars($val))."</td>\n";
                        }

                        echo "</tr>\n\t<tr class='liste'>".$str."</tr>\n";

                        while($donnees = $this->Resultat->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "\t<tr class='liste'>\n";

                            foreach($donnees as $cle=>$valeur)
                            {
                                echo "\t\t<td class='liste'>".nl2br(htmlspecialchars($valeur))."</td>\n";
                            }

                            echo "\t</tr>\n";
                        }

                        echo "</table>\n";
                        $this->Resultat->closeCursor();
                    }
                    catch(Exception $e)
                    {
                        echo "<p>Erreur : requête incorrecte.<br />".($this->debugMode==true ? $this->LaRequete."<br />" : "").$e->getMessage()."</p>";
                    }
                }
                catch (Exception $e)
                {
                    echo "<p>Erreur : Impossible d'afficher les résultats de la requête.<br />".($this->debugMode==true ? $this->LaRequete."<br />" : "").$e->getMessage()."</p>";
                }
            }
            else
            {
                echo "<p>Pas de résultat.</p>";
            }
        }
    }

    //RETOURNE TOUS LES ENREGISTREMENTS SOUS FORME D'UN TABLEAU
    public function ReturnTout()
    {
        // echo "<p>requete: ".$this->LaRequete."</p>";
        if ($this->LaRequete=="")
        {
            return "Requête vide";
        }
        else
        {
            $retour = $this->Resultat->fetchAll();
            return $retour;
        }
    }

    //RETOURNE LA VALEUR DU CHAMP CHOISI EN FOUNCTION DU TUPLE EN COURS
    public function Valeur($champ)
    {
        return $this->Curseur[$champ];
    }

    //PASSE TUPLE SUIVANT
    public function NextLine()
    {
        try
        {
            while($this->Curseur = $this->Resultat->fetch(PDO::FETCH_ASSOC))
            {
                return true;
            }
            return false;
        }
        catch(Exception $e)
        {
            echo "<p>Erreur : <br />".$e->getMessage()."</p>";
            return false;
        }
    }

    //LIBÈRE LES VARIABLES
    public function __destruct()
    {
        unset($pdo);
        unset($Resultat);
    }
}
