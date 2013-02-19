<script id="new-user-template" type="text/template">
  <% if( response ) { %>
    <h1 class="response"><%= response %></h1>
  <% } %>
  <form class="user-form" id="signup-form" method="post" action="/blog/user/create">
    <h1 class="form-title">Welcome. Ish.</h1>
    <h2 class="form-subtitle">sign up</h2>
    <input type="text" class="username" name="username" placeholder="Username">
    <input type="text" class="first-name" name="first_name" placeholder="First name">
    <input type="text" class="last-name" name="last_name" placeholder="Last name">
    <input type="password" class="password" name="password" placeholder="Password">
    <input type="password" class="password-confirmation" name="password_confirmation" placeholder="Password Confirmation">
    <input type="submit" value="Sign Up">
    <div class="clear"></div>
  </form>
</script>