<?php

class Authenticate {

  function __construct( $hasher ) {
    $this->hasher = $hasher;
  }
  
  function login( $username, $password ) {
    $user = ORM::for_table( 'users' )->where( 'username', $username )->find_one();
    if( $user != null && $this->hasher->checkPassword( $password, $user->hashed_password ) ) {
      $_SESSION['current_user'] = $user;
      return $user; 
    } else {
      return "Incorrect username/password combination.";
    }
  }
  
  function logout() {
    unset( $_SESSION['current_user'] );
  }
  
  function signup( $username, $password, $first_name, $last_name ) {
    if( $username == "" ) {
      return "Username cannot be blank.";
    } else if( $password == "" ) {
      return "Password cannot be blank.";
    } else if( $first_name == "" || $last_name == "" ) {
      return "Name cannot be blank.";
    }
    $user = ORM::for_table( 'users' )->where( 'username', $username )->find_one();
    if( empty( $user ) ) {
      $user = ORM::for_table( 'users' )->create();
      $user->username = $username;
      $user->first_name = $first_name;
      $user->last_name = $last_name;
      $user->hashed_password = $this->hasher->hashPassword( $password );
      $user->api_token = $this->generate_api_token();
      $user->save();
      if( empty( $user->id ) ) {
        return "User did not save. Try again.";
      } else {
        $_SESSION['current_user'] = $user;
        return $user;
      }
    } else {
      return "Username has been taken.";
    }
  }
  
  function generate_api_token() {
    $letters= array(
      'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q',"r","s",'t','u','v','w','x','y','z',
      'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
      '0','1','2','3','4','5','6','7','8','9'
    );
    $api_token_array = array();
    while( count( $api_token_array ) < 140 ) {
      array_push( $api_token_array, $letters[array_rand( $letters )] );
    }
    return implode( $api_token_array, '' );
  }
  
}
