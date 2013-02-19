$(document).ready( function() {
  
  window.AppRouter = Backbone.Router.extend({

    routes: {
      '':             'take_me_home_jeeves',
      'login/':       'login',
      'logout/':      'logout',
      'user/new':     'new_user',
      'user/all':     'all_users',
      'book/new':     'new_book',
      'book/all':     'all_books',
      'book/search':  'search_books'
    },
    
    take_me_home_jeeves: function() {
      var status = new window.Status()
      status.fetch({
        success: function() {
          var app_view = new window.AppView({ model: status });
          app_view.render();          
        }
      });

    },
    
    login: function() {
      var status = new window.Status();
      status.fetch({
        success: function() {
          if( $('.content').length < 1 ) {
            var app_view = new window.AppView({ model: status });
            app_view.render();
          }
          var login_view = new window.LoginView();
          login_view.render( false );                    
        }
      });
    },
    
    logout: function() {
      $.ajax({
        type: 'get',
        url: 'logout/',
        success: function() {
          Backbone.history.navigate( '#', true );
        }
      });
    },

    all_users: function() {
      var status = new window.Status();
      status.fetch({
        success: function() {
          if( $('.content').length < 1 ) {
            var app_view = new window.AppView({ model: status });
            app_view.render();
          }
          if( status.get('is_logged_in') ) {
            var user_table_view = new window.UserTableView();
            user_table_view.render();
          } else {
            Backbone.History.navigate( '#login', true )
          }
        }
      });
    },
    
    new_user: function() {
      var status = new window.Status();
      status.fetch({
        success: function() {
          if( $('.content').length < 1 ) {
            var app_view = new window.AppView({ model: status });
            app_view.render();
          }
          if( status.get('is_logged_in') ) {
            var new_user_view = new window.NewUserView();
            new_user_view.render( false );          
          } else {
            Backbone.History.navigate( '#login', true )
          }
        }
      });
    },
    
    new_book: function() {
      var status = new window.Status();
      status.fetch({
        success: function() {
          if( $('.content').length < 1 ) {
            var app_view = new window.AppView({ model: status });
            app_view.render();
          }
          if( status.get('is_logged_in') ) {
            var new_book_view = new window.NewBookView();
            new_book_view.render( false );
          } else {
            Backbone.History.navigate( '#login', true )
          }
        }
      })
    },
    
    all_books: function() {
      var status = new window.Status();
      status.fetch({
        success: function() {
          if( $('.content').length < 1 ) {
            var app_view = new window.AppView({ model: status });
            app_view.render();
          }
          if( status.get('is_logged_in') ) {
            var book_table_view = new window.BookTableView();
            book_table_view.render();
          } else {
            Backbone.History.navigate( '#login', true )
          }
        }
      });
    },
    
    search_books: function() {
      var status = new window.Status();
      status.fetch({
        success: function() {
          if( $('.content').length < 1 ) {
            var app_view = new window.AppView({ model: status });
            app_view.render();
          }
          if( status.get('is_logged_in') ) {
            var book_search_view = new window.BookSearchFormView();
            book_search_view.render();
          } else {
            Backbone.History.navigate( '#login', true )
          }
        }
      });
    }
    
  });
  
  
  var router = new window.AppRouter();
  Backbone.history.start();
  
});