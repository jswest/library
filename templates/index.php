<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Hawthorne Street Library</title>
    <link rel="stylesheet" type="text/css" media="all" href="/library/assets/css/style.css">
    <script src="/library/assets/js/jquery.js" type="text/javascript"></script>
    <script src="/library/assets/js/jquery.autocomplete.js" type="text/javascript"></script>
    <script src="/library/assets/js/underscore.js" type="text/javascript"></script>
    <script src="/library/assets/js/backbone.js" type="text/javascript"></script>
    <script src="/library/assets/js/app.js" type="text/javascript"></script>
    <script src="/library/assets/js/users.js" type="text/javascript"></script>
    <script src="/library/assets/js/book.js" type="text/javascript"></script>
    <script src="/library/assets/js/router.js" type="text/javascript"></script>
  </head>
  <body>
    <script id="app-template" type="text/template">
      <header>
        <hgroup>
          <% if( response ) { %>
            <h2>welcome, <span class="username"><%= username %></span>, to</h2>
          <% } else { %>
            <h2>welcome to</h2>
          <% } %>
          <h1>THE HAWTHORNE STREET LIBRARY</h1>
        </hgroup>
      </header>
      <nav>
        <ul>
          <% if( response ) { %>
            <li><a href="#logout/">Logout</a></li>
            <li>
              Users
              <ul>
                <li><a href="#user/all">All Users</a></li>
                <li><a href="#user/new">New User</a></li>
              </ul>
            </li>
            <li>
              Books
              <ul>
                <li><a href="#book/search">Search Books</a></li>
                <li><a href="#book/new">New Book</a></li>
              </ul>
            </li>
          <% } else { %>
            <li><a href="#login/">Login</a></li>        
          <% } %>
        </ul>
      </nav>
      <div class="content"></div>
    </script>
    <?php
      require_once 'login/new.php';
      require_once 'user/new.php';
      require_once 'user/all.php';
      require_once 'book/new.php';
      require_once 'book/all.php';
    ?>
    <div class="page-wrapper"></div>
  </body>
</html>