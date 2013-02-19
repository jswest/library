$(document).ready( function() {
  
  window.LoginView = Backbone.View.extend({
    
    className: 'user-page',
    
    id: 'login-page',
    
    events: {
      'submit #login-form': 'form_submit'
    },
    
    initialize: function() {
      _.bindAll( this, 'render', 'form_submit' );
      $(this.el).find('form').bind('keypress', false);
    },
    
    render: function( response ) {
      var template = _.template( $('#login-template').html(), { 'response': response } ); 
      $('.content').html( $(this.el).html( template ) );
    },
    
    form_submit: function() {
      var that = this;
      $.ajax({
        data: {
          username: $(that.el).find( '.username' ).val(),
          password: $(that.el).find( '.password' ).val()
        },
        type: 'post',
        url: 'login/',
        success: function( data ) {
          data = JSON.parse( data );
          if( data['response'] == false ) {
            Backbone.history.navigate( '#', true );
          } else {
            var login_view = new window.LoginView();
            login_view.render( data['response'] );
          }
        }
      });
      return false;
    }
    
  });
  
  window.UserTr = Backbone.Model.extend({
  });
  window.Users = Backbone.Collection.extend({
    model: window.UserTr,
    url: 'user/all/'
  });
  window.UserTableView = Backbone.View.extend({
    className: 'user-page',
    id: 'all-users-page',
    initialize: function() {
      _.bindAll( this, 'render' );
    },
    render: function() {
      var template = _.template( $('#users-template').html(), {} );
      $('.content').html( $(this.el).html( template ) );
      var users = new window.Users();
      users.fetch({
        success: function() {
          users.each( function( user ) {
            var user_tr_view = new UserTrView({ model: user });
            user_tr_view.render();
          });
        }
      });
    }
  });
  window.UserTrView = Backbone.View.extend({
    tagName: 'tr',
    initialize: function() {
      _.bindAll( this, 'render' );
    },
    render: function() {
      var template = _.template( $('#user-tr-template').html(), this.model.toJSON() );
      $('table.users').append( $(this.el).html( template ) );
    }
  });
  
  window.NewUserView = Backbone.View.extend({
    className: 'user-page',
    id: 'new-user-page',
    events: {
      'submit #signup-form': 'form_submit'
    },
    initialize: function() {
      _.bindAll( this, 'render', 'form_submit' );
    },
    render: function( response ) {
      var template = _.template( $('#new-user-template').html(), { 'response': response } );
      $('.content').html( $(this.el).html( template ) );
    },
    form_submit: function() {
      var that = this;
      $.ajax({
        data: {
          username: $(this.el).find( '.username' ).val(),
          first_name: $(this.el).find( '.first-name' ).val(),
          last_name: $(this.el).find( '.last-name' ).val(),
          password: $(this.el).find( '.password' ).val(),
          password_confirmation: $(this.el).find( '.password-confirmation' ).val()
        },
        type: 'post',
        url: 'user/create/',
        success: function( data ) {
          data = JSON.parse( data );
          if( data['response'] == false ) {
            Backbone.history.navigate( '#', true );
          } else {
            var new_user_view = new window.NewUserView();
            new_user_view.render( data['response'] );
          }
        }
      });
      return false;
    }
  });
  
});