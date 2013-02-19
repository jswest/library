<script id="users-template" type="text/template">
  <table class="users">
    <tr>
      <th>Username</th>
      <th>Last Name</th>
      <th>First Name</th>
      <th>User Page</th>
    </tr>    
  </table>
</script>

<script id="user-tr-template" type="text/template">
  <td><%= username %></td>
  <td><%= last_name %></td>
  <td><%= first_name %></td>
  <td><a href="/user/<%= id %>/show">User Page</a></td>
</script>