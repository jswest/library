<script id="books-search-template" type="text/template">
  <form class="search-form">
    <input type="text" class="book-search" name="book-search" placeholder="Search books">
    <div class="clear"></div>
  </form>
  <table class="books">
  </table>
</script>


<script id="books-template" type="text/template">
  <table class="books">
    <tr>
      <th>Title</th>
      <th>Color</th>
      <th>Authors</th>
      <th>Tags</th>
      <th>Completed</th>
      <th>Checked Out</th>
      <th>Edit</th>
      <th>Delete</th>
    </tr>
  </table>
</script>

<script id="book-tr-template" type="text/template">
  <td><%= title %></td>
  <td><%= color %></td>
  <td>
    <% for( var i = 0; i < authors.length; i++ ) { %>
      <a href="#author/<%= authors[i]['id'] %>/show"><%= authors[i]['first_name'] %> <%= authors[i]['last_name'] %></a>
    <% } %>
  </td>
  <td>
    <% for( var i = 0; i < tags.length; i++ ) { %>
      <a href="#tag/<%= tags[i]['id'] %>/show"><%= tags[i]['name'] %></a>
    <% } %>
  </td>
  <td><%= completed %></td>
  <td><%= checked_out %></td>
  <td><a href="#book/<%= id %>/show">Edit</a></td>
  <td><a href="#book/<%= id %>/delete">Delete</a></td>
</script>