<script id="login-template" type="text/template">
  <% if( response ) { %>
    <h1 class="response"><%= response %></h1> 
  <% } %>
  <form id="login-form" class="user-form" method="post" action="/login">
    <h1 class="form-title">Welcome. Ish.</h1>
    <input type="text" class="username" name="username" placeholder="Username...">
    <input type="password" class="password" name="password" placeholder="Password...">
    <input type="submit" value="Log in">
    <div class="clear"></div>
  </form>
</script>

