<script id="new-book-template" type="text/template">
  <% if( response ) { %>
    <h1 class="response"><%= response %></h1>
  <% } %>
  <form class="book-form" id="new-book-form" method="post" action="/blog/book/create">
    <h1 class="form-title">Welcome.</h1>
    <h2 class="form-subtitle">create a book</h2>
    <input type="text" class="title" name="title" placeholder="Title">
    <input type="text" class="color" name="color" placeholder="Color">
    <div class="authors">
      <input type="text" class="author" name="author" placeholder="Author">
    </div>
    <div class="tags">
      <input type="text" class="tag" name="tag" placeholder="Tag">
    </div>
    <input type="submit" value="Create Book">
    <div class="clear"></div>
  </form>
</script>