<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;


class User extends Model {

    const SESSION = "User";

    public static function login($login, $password) {

        $sql =  new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array (
            ":LOGIN"=>$login
        ));
        if (count($results) === 0) {
            throw new \Exception("Usuário não encontrado ou senha inválida.");
        }

        $data = $results[0];

        if (password_verify($password, $data["despassword"]) === true) {
            $user = new User();

            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getValues();

            return $user;

        } else (
            throw new \Exception("Usuário não encontrado ou senha inválida.")
        );

    }
    public static function verifyLogin($inadmin = true) {
        if (
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0
            ||
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin

        ) {
            header("Location: /admin/login");
            exit;
        }
    }

    public static function logout() {
        $_SESSION[User::SESSION] = NULL;
    }

    public static function listAll() {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
    }

    public static function createPerson($desperson, $desemail, $nrphone) {
        $sql = new Sql();
        return $sql->create("INSERT INTO tb_persons (desperson, desemail, nrphone) VALUES(:desperson, :desemail, :nrphone)", array(
            "desperson"=> $desperson,
            "desemail"=> $desemail,
            "nrphone"=> $nrphone,
        ));
    }

    public static function createUser($idperson, $deslogin, $despassword, $inadmin) {
        $sql = new Sql();
        return $sql->create("INSERT INTO tb_users (idperson, deslogin, despassword, inadmin) VALUES(:idperson, :deslogin, :despassword, :inadmin)", array(
            "idperson"=> $idperson,
            "deslogin"=> $deslogin,
            "despassword"=> $despassword,
            "inadmin"=> $inadmin
        ));
    }

}
?>