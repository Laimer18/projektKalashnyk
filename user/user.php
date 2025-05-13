<?php   
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class User
{
    public ?int $id;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $phone;
    public string $password;

    public function __construct($first_name, $last_name, $email, $phone, $id = null, $password = '')
    {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone = $phone;
        $this->password = $password;
    }
}