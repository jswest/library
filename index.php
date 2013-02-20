<?php

require_once 'library/Slim/Slim.php';
require_once 'library/idiorm/idiorm.php';
require_once 'library/phpass/src/Phpass.php';
require_once 'library/authenticate/authenticate.php';

ORM::configure( 'mysql:host=localhost;dbname=library_db' );
ORM::configure('username', 'root');
ORM::configure('password', 'root');

$hasher = new \Phpass\Hash;

session_cache_limiter( false );
session_start();

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim(
  array(
    'debug' => true
  )
);


/*
 * DEFINE THOSE ROUTES
*/ 
// Root (GET)
$app->get( '/', function() use( $app ) {
  $app->render(
    '/index.php',
    array(),
    200
  );
});


$app->get( '/status/', function() use( $app ) {
  if( isset( $_SESSION['current_user'] ) ) {
    $response = array(
      'is_logged_in' => true,
      'username' => $_SESSION['current_user']->username,
      'api_token' => $_SESSION['current_user']->api_token
    );
  } else {
    $response = false;
  }
  header( "Content-Type: application/json" );
  echo json_encode( $response ); 
});



/*
 * User CRUD Methods
 * These methods control the user CRUD actions.
 *
 */
$app->post( '/user/create/', function() use( $app, $hasher ) {
  
  // define some variables
  $password = $app->request()->params( 'password' );
  $username = $app->request()->params( 'username' );
  $username = strtolower( $username );
  $first_name = $app->request()->params( 'first_name' );
  $last_name = $app->request()->params( 'last_name' );
  $password_confirmation = $app->request()->params( 'password_confirmation' );
  
  
  if( $password == $password_confirmation ) {
    $authenticator = new Authenticate( $hasher );
    $response = $authenticator->signup( $username, $password, $first_name, $last_name );
    if( !is_string( $response ) ) {
      $response = false;
    }
  } else {
    $response = "Password and password confirmation do not match.";
  }
  header( "Content-Type: application/json" );
  echo json_encode( array( 'response' => $response ) );
});
$app->get( '/user/all/', function() use( $app ) {
  $raw_users = ORM::for_table( 'users' )->find_many();
  $users = array();
  foreach( $raw_users as $raw_user ) {
    $user = array();
    $user['id'] = $raw_user->id;
    $user['username'] = $raw_user->username;
    $user['first_name'] = $raw_user->first_name;
    $user['last_name'] = $raw_user->last_name;
    array_push( $users, $user );
  }
  header( "Content-Type: application/json" );
  echo json_encode( $users );
});



/*
 * Session CRUD methods
 * These methods control the login/logout actions
 *
 */
$app->post( '/login/', function() use( $app, $hasher ) {
  $username = $app->request()->params( 'username' );
  $username = strtolower( $username );
  $password = $app->request()->params( 'password' );
  $authenticator = new Authenticate( $hasher );
  $response = $authenticator->login( $username, $password);
  if( !is_string( $response ) ) {
    $response = false;
  }
  header( "Content-Type: application/json" );
  echo json_encode( array( 'response' => $response ) );  
});

$app->get( '/logout/', function() use( $app, $hasher ) {
  $authenticator = new Authenticate( $hasher );
  $authenticator->logout();
});



/*
 * Book CRUD methods
 * These methods control the login/logout actions
 *
 */
$app->get( '/book/search/', function() use( $app) {
  $query = $app->request()->params( 'query' );
  $query = strtolower( $query );
  if( strlen( $query ) > 2 ) {
    $query_string = $query;
    $query = explode( ' ', $query );
    $potential_matches = array();
    $raw_potential_matches = array();
  
    // add the books by title
    $books = ORM::for_table( 'books' )->where_like( 'title', "%$query_string%" )->find_many();
    foreach( $books as $book ) {
      if( !in_array( $book, $raw_potential_matches ) ) {
        array_push( $raw_potential_matches, $book );
      }
    }
  
    // add the books by author
    if( count( $query ) == 1 ) {
      $first_name_authors = ORM::for_table( 'authors' )->where_like( 'first_name', "%$query[0]%" )->find_many();
      $last_name_authors = ORM::for_table( 'authors' )->where_like( 'last_name', "%$query[0]%" )->find_many();    
    } elseif( count( $query ) == 2 ) {
      $first_name_authors = ORM::for_table( 'authors' )->where_like( 'first_name', "%$query[0]%" )->find_many();
      $last_name_authors = ORM::for_table( 'authors' )->where_like( 'last_name', "%$query[1]%" )->find_many();    
    } else {
      $first_name_authors = array();
      $last_name_authors = array();
    }
    $authors = array();
    foreach( $first_name_authors as $first_name_author ) {
      array_push( $authors, $first_name_author );
    }
    foreach( $last_name_authors as $last_name_author ) {
      if( !in_array( $last_name_author, $authors ) ) {
        array_push( $authors, $last_name_author );
      }
    }
    foreach( $authors as $author ) {
      $books_authors = ORM::for_table( 'books_authors' )->where( 'author_id', $author->id )->find_many();
      foreach( $books_authors as $book_author ) {
        $book = ORM::for_table( 'books' )->where( 'id', $book_author->book_id )->find_one();
        if( !in_array( $book, $raw_potential_matches ) ) {
          array_push( $raw_potential_matches, $book );
        }
      }
    }
  
    // add the books by tag
    $tags = ORM::for_table( 'tags' )->where_like( 'name', "%$query_string%" )->find_many();
    foreach( $tags as $tag ) {
      $books_tags = ORM::for_table( 'books_tags' )->where( 'tag_id', $tag->id )->find_many();
      foreach( $books_tags as $book_tag ) {
        $book = ORM::for_table( 'books' )->where( 'id', $book_tag->book_id )->find_one();
        if( !in_array( $book, $raw_potential_matches ) ) {
          array_push( $raw_potential_matches, $book );
        }
      }
    }
  
    // put the raw books in the right format
    foreach( $raw_potential_matches as $raw_potential_match ) {
      $book = array();
      $book['title'] = $raw_potential_match->title;
      $book['color'] = $raw_potential_match->color;
      $book['id'] = $raw_potential_match->id;
      if( $raw_potential_match->completed ) {
        $book['completed'] = "completed";
      } else {
        $book['completed'] = "not completed";
      }
      if( $raw_potential_match->checked_out ) {
        $book['checked_out'] = "checked out";
      } else {
        $book['checked_out'] = "not checked out";
      }
      $author_ids = ORM::for_table( 'books_authors' )->where( 'book_id', $raw_potential_match->id )->find_many();
      $authors = array();
      foreach( $author_ids as $author_id ) {
        $author = array();
        $raw_author = ORM::for_table( 'authors' )->where( 'id', $author_id->author_id )->find_one();
        $author['first_name'] = $raw_author->first_name;
        $author['last_name'] = $raw_author->last_name;
        $author['id'] = $raw_author->id;
        array_push( $authors, $author );
      }
      $book['authors'] = $authors;
      $tag_ids = ORM::for_table( 'books_tags' )->where( 'book_id', $raw_potential_match->id )->find_many();
      $tags = array();
      foreach( $tag_ids as $tag_id ) {
        $tag = array();
        $raw_tag = ORM::for_table( 'tags' )->where( 'id', $tag_id->tag_id )->find_one();
        $tag['name'] = $raw_tag->name;
        $tag['id'] = $raw_tag->id;
        array_push( $tags, $tag );
      }
      $book['tags'] = $tags;
      array_push( $potential_matches, $book );
    }
  }
  header( "Content-Type: application/json" );
  echo json_encode( $potential_matches );
});


$app->get( '/book/all/', function()  use( $app ) {
  $raw_books = ORM::for_table( 'books' )->find_many();
  $books = array();
  foreach( $raw_books as $raw_book ) {
    $book = array();
    $book['title'] = $raw_book->title;
    $book['color'] = $raw_book->color;
    $book['id'] = $raw_book->id;
    if( $raw_book->completed ) {
      $book['completed'] = "completed";
    } else {
      $book['completed'] = "not completed";
    }
    if( $raw_book->checked_out ) {
      $book['checked_out'] = "checked out";
    } else {
      $book['checked_out'] = "not checked out";
    }
    $author_ids = ORM::for_table( 'books_authors' )->where( 'book_id', $raw_book->id )->find_many();
    $authors = array();
    foreach( $author_ids as $author_id ) {
      $author = array();
      $raw_author = ORM::for_table( 'authors' )->where( 'id', $author_id->author_id )->find_one();
      $author['first_name'] = $raw_author->first_name;
      $author['last_name'] = $raw_author->last_name;
      $author['id'] = $raw_author->id;
      array_push( $authors, $author );
    }
    $book['authors'] = $authors;
    $tag_ids = ORM::for_table( 'books_tags' )->where( 'book_id', $raw_book->id )->find_many();
    $tags = array();
    foreach( $tag_ids as $tag_id ) {
      $tag = array();
      $raw_tag = ORM::for_table( 'tags' )->where( 'id', $tag_id->tag_id )->find_one();
      $tag['name'] = $raw_tag->name;
      $tag['id'] = $raw_tag->id;
      array_push( $tags, $tag );
    }
    $book['tags'] = $tags;
    array_push( $books, $book );
  }
  header( "Content-Type: application/json" );
  echo json_encode( $books );
});

$app->get( '/author/find/', function() use( $app ) {
  $query = $app->request()->params( 'query' );
  $query_array = explode( ' ', $query );
  foreach( $query_array as $name ) {
    $first_names = ORM::for_table( 'authors' )->where_like( 'first_name', "%$name%" )->find_many();
    $last_names = ORM::for_table( 'authors' )->where_like( 'last_name', "%$name%" )->find_many();    
  }
  $authors = array();
  foreach( $first_names as $raw_author ) {
    $author = "$raw_author->first_name $raw_author->last_name";
    array_push( $authors, $author );
  }
  foreach( $last_names as $raw_author ) {
    $author = "$raw_author->first_name $raw_author->last_name";
    if( !in_array( $author, $authors ) ) {
      array_push( $authors, $author );      
    }
  }
  $response = array();
  $response['query'] = $query;
  $response['suggestions'] = $authors;
  header( "Content-Type: application/json" );
  echo json_encode( $response );  
});

$app->get( '/tag/find/', function() use( $app ) {
  $query = $app->request()->params( 'query' );
  $raw_tags = ORM::for_table( 'tags' )->where_like( 'name', "%$query%" )->find_many();
  $tags = array();
  foreach( $raw_tags as $raw_tag ) {
    $tag = $raw_tags['name'];
    array_push( $tags, $tag );
  }
  $response = array();
  $response['query'] = $query;
  $response['suggestions'] = $tags;
  header( "Content-Type: application/json" );
  echo json_encode( $response );
});

$app->post( '/book/create/', function() use( $app ) {
  $title = $app->request()->params( 'title' );
  $color = $app->request()->params( 'color' );
  $raw_completed = $app->request()->params( 'completed' );
  if( $raw_completed == "true" ) {
    $completed = true;
  } else {
    $completed = false;
  }
  $raw_checked_out = $app->request()->params( 'checked_out' );
  if( $raw_checked_out == "true" ) {
    $checked_out = true;
  } else {
    $checked_out = false;
  }
  $raw_authors = $app->request()->params( 'authors' );
  $raw_tags = $app->request()->params( 'tags' );
  $owner_username = $_SESSION['current_user']->username;
  $owner = ORM::for_table( 'users' )->where( 'username', $owner_username )->find_one();
  $owner_id = $owner->id;
  
  $book = ORM::for_table( 'books' )->create();;
  $book->title = $title;
  $book->color = $color;
  $book->completed = $completed;
  $book->checked_out = $checked_out;
  $book->owner = $owner_id;
  $book->save();
  
  $authors = array();
  foreach( $raw_authors as $raw_author ) {
    $raw_author_array = explode( ' ', $raw_author );
    if( count( $raw_author_array ) == 2 ) {
      $raw_author_first_name = $raw_author_array[0];
      $raw_author_last_name = $raw_author_array[1];
    
      $author_pool = ORM::for_table( 'authors' )->where( 'last_name', $raw_author_last_name )->find_many();
      $existant_author = array();
      if( count( $author_pool ) > 1 ) {
        foreach( $author_pool as $pool_item ) {
          if( $pool_item->first_name == $raw_author_first_name ) {
            $existant_author = $pool_item;
            array_push( $authors, $existant_authors );
          }
        }
      } elseif( count( $author_pool ) == 1 ) {
        $existant_author = $author_pool[0];
        array_push( $authors, $existant_author );
      }
      if( empty( $existant_author ) ) {
        $author = ORM::for_table( 'authors' )->create();
        $author->first_name = strtolower( $raw_author_first_name );
        $author->last_name = strtolower( $raw_author_last_name );
        $author->save();
        array_push( $authors, $author );
      }
    }
  }
  foreach( $authors as $author ) {
    $book_author = ORM::for_table( 'books_authors' )->create();
    $book_author->book_id = $book->id;
    $book_author->author_id = $author->id;
    $book_author->save();
  }
  
  $tags = array();
  foreach( $raw_tags as $raw_tag ) {
    $existant_tag = ORM::for_table( 'tags' )->where( 'name', $raw_tag )->find_one();
    if( empty( $existant_tag ) ) {
      if( $raw_tag != "" ) {
        $tag = ORM::for_table( 'tags' )->create();
        $tag->name = strtolower( $raw_tag );
        $tag->save();
      }
    } else {
      $tag = $existant_tag;
    }
    array_push( $tags, $tag );
  }
  if( count( $tags ) > 0 ) {
    foreach( $tags as $tag ) {
      $book_tag = ORM::for_table( 'books_tags' )->create();
      $book_tag->book_id = $book->id;
      $book_tag->tag_id = $tag->id;
      $book_tag->save();
    }
  }
  $response = false;
  header( "Content-Type: application/json" );
  echo json_encode( array( 'response' => $response ) );
  
});




/*
 * DO IT!
 */
$app->run();
