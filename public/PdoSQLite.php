<?php

namespace SYRADEV\db;

use PDO, PDOException;


// Classe de connexion à une database SQLite en singleton

//>>> Design Pattern : Patron de conception


final class PdoSQLite {

    private static ?PdoSQLite $connect = null;
    private string $dbfile ='C:/xampp/localsites/users.sqlite3';
    private PDO $cnx;

    private function __construct() {

        try {

            $this->cnx = new PDO('sqlite:'. $this->dbfile, '', '', [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

        } catch(PDOException $e) {

            $message = 'Erreur PDO !'.$e->getMessage().'<hr>';
            die($message);
        }
    }


    public static function getInstance():PdoSQLite
    {
        if(is_null(self::$connect)) {
            self::$connect = new PdoSQLite();
        }
        return self::$connect;
    }

    public function requete($sql, $fetchMethod='fetchAll')
    {
        try {
            $result = $this->cnx->query($sql)->{$fetchMethod}();
        } catch(PDOException $e) {
            $message = 'Erreur de requete!' . $e->getMessage().'<hr>';
            die($message);
        }
        return $result;

    }
    public function inserer(string $table, array $data): bool
    {

        // On récupère les nom de champs dans les clés du tableau
        $fields = array_keys($data);
        // On récupère les valeurs
        $values = array_values($data);

        // On compte le nombre de champ
        $values_count = count($values);

        // On construit la chaine des paramètres ':p0,:p1,:p2,...'
        $params = [];
        foreach ($values as $key => $value) {
            // array_push($params, ':p' . $key);
            $params[] = ':p' . $key;
        }
        $params_str = implode(',', $params);

        // On prépare la requête
        $reqInsert = 'INSERT INTO ' . $table . '('. implode(',',$fields).')';
        $reqInsert .= ' VALUES('.$params_str.')';

        $prepared = $this->cnx->prepare($reqInsert);

        // On injecte dans la requête les données avec leur type.
        for($i=0;$i<$values_count;$i++) {
            $type = match (gettype($values[$i])) {
                'NULL' => PDO::PARAM_NULL,
                'integer' => PDO::PARAM_INT,
                'boolean' => PDO::PARAM_BOOL,
                default => PDO::PARAM_STR,
            };
            // On lie une valeur au paramètre :pX
            $prepared->bindParam(':p'.$i, $values[$i], $type);
        }

        // On exécute la requête.
        // Retourne TRUE en cas de succès ou FALSE en cas d'échec.
        return $prepared->execute();
    }

}




// $db = PdoSQLite::getInstance();
// $users = $db->requete('SELECT * FROM users');
// echo '<pre>';
// print_r($users);
// echo '</pre>';