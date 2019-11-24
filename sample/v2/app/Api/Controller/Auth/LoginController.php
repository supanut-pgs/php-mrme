<?php
namespace Api\Controller\Auth;

use MrMe\Database\DB;
use MrMe\Web\v2\AuthController;
use MrMe\Web\v2\Request;

use PDO;

class LoginController extends AuthController
{
    public function login()
    {
        
        $this->request->accept("POST");
        $this->request->validate([
            "username" => "body|required",
            "password" => "body|required",
        ]);
    
        $username = $this->request->body->username;
        $password = $this->request->body->password; 


        // DB::all();

        $sql = "SELECT * FROM `users` ";
        $sql.= "#WHERE username=:user ";
        $sql.= "#AND `password`=:pass";
        
        $params = [
            ":user" => "test15",
            ":pass" => "1234s"
        ];
        //dd(DB::query($sql)->execute($params)->first());
        //dd(DB::table("users")->execute()->all());
        // DB::table('users')->insert([
        //     "username" => "test4",
        //     "password" => "1234"
        // ])->execute();

        // dd(DB::table('users')->update([
        //     "username" => "xxx",
        //     "password" => "1324"
        // ])->where("id = 3")->execute());
        // DB::table('users')->update([
        //     "username" => ":user",
        //     "password" => ":pass"
        // ])->execute($params);

        //dd(DB::table("users")->last());

        $data = DB::table("users")->select(["username"])->execute()->all();

        dd($data);
        if ($username == "test" && $password == "1234")
            $this->response->success([
                "success"   => true,
                "token"     => "123456"
            ]);
        else 
            $this->response->unauthorized([
                "success"   => false,
                "message"   => "Username or Password not mactched."
            ]);
    }
    public function logout()
    {
        dd("This is logout");
    }
}
