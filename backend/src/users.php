<?php

namespace cbos;

class users {
    public $hasura;

    public function __construct($hasura) {
        $this->hasura = $hasura;
    }

    public function get_hello() {
        return '** hello users **';
    }

    public function authenticate($data) {

        dbg('++++ Input', $data); 

        dbg('++++ Login', $data['username']);
        dbg('++++ Password', $data['password']);

        // get user data from hasura
        $resp = post_graphql($this->hasura, 'get_users', ['login' => $data['username']]);
        /* if ($resp['errors']) {
            return $resp;
        } */
        dbg('++++ Get users from Hasura', $resp);  
        
        // there should exactly only one result
        if(count($resp['data']['users']) == 1) {

            $userdata =  $resp['data']['users'][0];

            // check password
            if(password_verify($data['password'], $userdata['password'])) {

                // Timeout nach 5 Minuten
                $exp = time() + (60*5);

                // store in session
                $_SESSION['id'] = $userdata['id'];
                $_SESSION['login'] = $data['username'];
                $_SESSION['vorname'] = $userdata['vorname'];
                $_SESSION['nachname'] = $userdata['nachname'];
                $_SESSION['email'] = $userdata['email'];
                $_SESSION['rolle'] = $userdata['rolle'];

                $user = $this->get_user_from_session();
                $user['expire'] = $exp;
                $token = generate_jwt('we-operate.com', $userdata['id'], $userdata['email'], 'weoperate', $exp, $userdata['rolle']);

                setcookie('hasuraaccess', $token, 0, '/');
            } else {
                // password failed
                $user['id'] = 0;
            }

        } else {
            // user not found
            $user['id'] = 0;
        }

        return $user;
    }

    public function refresh_token() {

        if($_SESSION['id']) {
            // Timeout nach 5 Minuten
            dbg('++++ Generate new token for user');
            $exp = time() + (60*5);

            $user = $this->get_user_from_session();
            $user['expire'] = $exp;

            $token = generate_jwt('we-operate.com', $_SESSION['id'], $_SESSION['email'], 'weoperate', $exp, $_SESSION['rolle']);
            setcookie('hasuraaccess', $token, 0, '/');

        } else {
            // user not found
            $user['id'] = 0;           
        }

        return $user;
    }

    public function anonymous() {
        // get an anonymous token for some special access to hasura
        // get user data from hasura
        $resp = post_graphql($this->hasura, 'get_users', ['login' => 'anonymous']);
        /* if ($resp['errors']) {
            return $resp;
        } */
        dbg('++++ Get users from Hasura', $resp);  
        
        // there should exactly only one result
        if(count($resp['data']['users']) == 1) {

            $userdata =  $resp['data']['users'][0];

            // Timeout nach 5 Minuten
            $exp = time() + (60*5);

            // store in session
            $_SESSION['id'] = $userdata['id'];
            $_SESSION['login'] = $data['username'];
            $_SESSION['vorname'] = $userdata['vorname'];
            $_SESSION['nachname'] = $userdata['nachname'];
            $_SESSION['email'] = $userdata['email'];
            $_SESSION['rolle'] = $userdata['rolle'];

            $user = $this->get_user_from_session();
            $user['expire'] = $exp;
            $token = generate_jwt('we-operate.com', $userdata['id'], $userdata['email'], 'weoperate', $exp, $userdata['rolle']);

            setcookie('hasuraaccess', $token, 0, '/');
        } else {
            // password failed
            $user['id'] = 0;
        }

        return $user;
    }

    public function logout() {

        if($_SESSION['id']) {

            session_destroy();
            unset($_SESSION);

            setcookie('hasuraaccess', '', 0, '/');
        }
    }

    public function change_password($data) {

        // very simple first...
        $rc = true;

        dbg('++++ Input', $data); 

        dbg('++++ Id', $data['userid']);
        dbg('++++ Password', $data['password']);        

        // save new hashed password - currently DEFAULT is BCRYPT
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);

        $resp = post_graphql($this->hasura, 'update_password_for_user', ['id' => $data['userid'], 'password' => $hashed]);

        return $rc;
    }

    private function get_user_from_session() {

        $user['id'] = $_SESSION['id'];
        $user['login'] = $_SESSION['login'];
        $user['firstname'] = $_SESSION['vorname'];
        $user['lastname'] = $_SESSION['nachname'];
        $user['email'] = $_SESSION['email'];
        $user['rolle'] = $_SESSION['rolle'];

        return $user;
    }

}
