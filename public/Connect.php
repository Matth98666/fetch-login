<?php

namespace SYRADEV\Utils;

final class Connect
{
    private string $secret = '55KxHTn9SQsaQ9GzhimsbYq7X/DMZ3wj4hJuEynELy4=';
    private string $session_token_label = 'CSRF_TOKEN_SESS_IDX';
    private string $hashAlgo = 'sha3-512';
    private string $cipherAlgo = 'aes-256-cbc';
    private string $domain = 'www.myjs.org';
    private string $originating_url = 'https://www.myjs.org/';
    private string $session_name = 'FetchUserSession';
    protected static self|null $instance = null;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    /***
     * Instancie l'objet Connect en singleton
     * @return Connect *
     ***/
    public static function getInstance(): Connect
    {
        if (Connect::$instance === null) {
            Connect::$instance = new Connect;
        }
        return Connect::$instance;
    }

    /***
     * Initie une nouvelle session PHP si elle n'existe pas
     * @return void *
     ***/
    public function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.name', $this->session_name);
            ini_set('session.use_cookies', true);
            ini_set('session.use_only_cookies', false);
            ini_set('session.use_strict_mode', true);
            ini_set('session.cookie_httponly', true);
            ini_set('session.cookie_secure', true);
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.gc_maxlifetime', 3600);
            ini_set('session.cookie_lifetime', 3600);
            ini_set('session.use_trans_sid', true);
            ini_set('session.trans_sid_hosts', $this->domain);
            ini_set('session.referer_check', $this->originating_url);
            ini_set('session.cache_limiter', 'nocache');
            ini_set('session.sid_length', 128);
            ini_set('session.sid_bits_per_character', 6);
            ini_set('session.hash_function', $this->hashAlgo);
            session_start();
        }
    }

    /***
     * Protège l'accès à une page par session
     * @return void *
     ***/
    public function protectPage(): void
    {
        if(!isset($_SESSION['username']) || !isset($_SESSION['uid'])) {
            header('Location:login.php');
        }
    }

    /***
     * Génère un jeton CSRF
     * @return string *
     ***/
    public function generateCSRFToken(): string
    {
        if (empty($_SESSION[$this->session_token_label])) {
            $_SESSION[$this->session_token_label] = bin2hex(openssl_random_pseudo_bytes(256));
        }
        return hash_hmac($this->hashAlgo, $this->secret, $_SESSION[$this->session_token_label]);
    }

    /***
     * Valide une requête avec le jeton CSRF
     * @return bool *
     ***/
    public function validateAjaxRequest(): bool
    {
        if (!isset($_SESSION[$this->session_token_label])) {
            return false;
        }
        $expected = hash_hmac($this->hashAlgo, $this->secret, $_SESSION[$this->session_token_label]);
        $requestToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
        return hash_equals($requestToken, $expected);
    }

    /***
     * Valide un formulaire POST avec le jeton CSRF
     * @return bool *
     ***/
    public function validatePostRequest(): bool
    {
        if (!isset($_SESSION[$this->session_token_label])) {
            return false;
        }
        $expected = hash_hmac($this->hashAlgo, $this->secret, $_SESSION[$this->session_token_label]);
        $requestToken = $_POST['csrf-token'];
        return hash_equals($requestToken, $expected);
    }

    /***
     * Valide une requête ajax avec l'entête X_REQUESTED_WITH
     * @return bool *
     ***/
    public function ajaxCheck(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /***
     * Valide un domaine
     * @return bool *
     ***/
    public function domainCheck(): bool
    {
        $domain = $this->domain;
        return $_SERVER['HTTP_HOST'] === $domain && $_SERVER['SERVER_NAME'] === $domain;
    }

    /***
     * Encripte une valeur quelconque
     * @param mixed $value Valeur quelconque
     * @param string $passphrase Phrase secrète
     * @return string
     ***/
    public function aesEncrypt(mixed $value, string $passphrase): string
    {
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), $this->cipherAlgo, $key, true, $iv);
        $data = ["ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt)];
        return json_encode($data);
    }

    /***
     * Decripte une valeur préalablement encriptée
     * @param string $jsonStr Chaine JSON
     * @param string $passphrase Phrase secrète
     * @return mixed
     ***/
    public function aesDecrypt(string $jsonStr, string $passphrase): mixed
    {
        $json = json_decode($jsonStr, true);
        $salt = hex2bin($json["s"]);
        $iv = hex2bin($json["iv"]);
        $ct = base64_decode($json["ct"]);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = [];
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, $this->cipherAlgo, $key, true, $iv);
        return json_decode($data, true);
    }

    /***
     * Hash un texte avec l'algorithme Argon2id
     * puis convertit le hash en hexadécimal
     * @param $plaintext
     * @return string
     ***/
    public function argon2idHash($plaintext): string
    {
        return bin2hex(
            password_hash(
                hash_hmac($this->hashAlgo, $plaintext, $this->secret),
                PASSWORD_ARGON2ID,
                ['memory_cost' => 2**14, 'time_cost' => 5, 4]
            )
        );
    }

    /***
     * Compare un texte en clair
     * avec son hash argon2id convertit en hexadécimal
     * @param $plaintext
     * @param $hash
     * @return bool
     ***/
    public function argon2idHashVerify($plaintext, $hash): bool
    {
        return password_verify(
            hash_hmac(
                $this->hashAlgo,
                $plaintext,
                $this->secret),
            hex2bin($hash)
        );
    }
}