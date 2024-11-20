<?php

include_once("./../dbm/DbConnector.php");


class LogoffController {

    public function logout (){
        session_start();
        $_SESSION['user_email']=False;
        session_destroy();
        $tempoDecorrido = 0;
        $flag=1;
        header("Location:./../public/index.php");
    }

}
