<?php
    require '../vendor/autoload.php';
    require 'dao/UsersDao.class.php';


    Flight::register("userDao","UsersDao");

    Flight::route("GET /users",function(){
        Flight::json(Flight::userDao()->get_all());  
    });

    Flight::route("GET /users/@id", function($id){
        Flight::json(Flight::userDao()->get_by_id($id));
    });

    Flight::route("GET /usersById", function(){
        Flight::json(Flight::userDao()->get_by_id(Flight::request()->query['id']));
    });

    Flight::route("DELETE /users/@id", function($id){
        Flight::userDao()->delete_user($id);
        Flight::json(['message'=>'User by id ' . $id . ' has been deleted.']);
    });

    Flight::route("POST /users", function(){
        $data = Flight::request()->data->getData();
        $response = Flight::userDao()->add_user($data);
        Flight::json(['message'=>'User added sucessfully.','Data: ' => $response]);
        
    });

    Flight::route("PUT /users/@id", function($id){
        $data = Flight::request()->data->getData();
        $response = Flight::userDao()->update_user($data,$id);
        Flight::json(['message'=>'Updated user with new data.','Data'=> $response]);
    });
    Flight::start();
    
?>