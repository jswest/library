$(document).ready( function() {
  
  window.BookSearchFormView = Backbone.View.extend({
    className: 'book-page',
    id: 'search-books-page',
    events: {
      'keyup .book-search': 'on_form_keypress'
    },
    initialize: function() {
      _.bindAll( this, 'render' );
    },
    render: function() {
      var template = _.template( $('#books-search-template').html(), {} );
      $('.content').html( $(this.el).html( template ) );
    },
    on_form_keypress: function() {
      var query = $('.book-search').val();
      var books = new window.Books();
      books.url = 'book/search/?query=' + query;
      books.fetch({
        success: function() {
          $('table').html( '' );
          books.each( function( book ) {
            var book_tr_view = new window.BookTrView({ model: book });
            book_tr_view.render();
          });
        }
      });
    }
  });
  
  window.BookTr = Backbone.Model.extend({
  });
  window.Books = Backbone.Collection.extend({
    model: window.BookTr
  });
  window.BookTableView = Backbone.View.extend({
   className: 'book-page',
    id: 'all-books-page',
    initialize: function() {
      _.bindAll( this, 'render' );
    },
    render: function() {
      var template = _.template( $('#books-template').html(), {} );
      $('.content').html( $(this.el).html( template ) );
      var books = new window.Books();
      books.url = 'book/all/';
      books.fetch({
        success: function() {
          books.each( function( book ) {
            var book_tr_view = new window.BookTrView({ model: book });
            book_tr_view.render();
          });
        }
      });
    }
  });
  window.BookTrView = Backbone.View.extend({
    tagName: 'tr',
    initialize: function() {
      _.bindAll( this, 'render' );
    },
    render: function() {
      var template = _.template( $('#book-tr-template').html(), this.model.toJSON() );
      $('table').append( $(this.el).html( template ) );
    },
    render_for_search: function() {
      var template = _.template( $('#book-tr-template').html(), this.model.toJSON() );
      $('table').html( $(this.el).html( template ) );
    }
  });
  
  window.NewBookView = Backbone.View.extend({
    className: 'book-page',
    id: 'new-book-page',
    events: {
      'submit #new-book-form': 'form_submit'
    },
    initialize: function() {
      _.bindAll( this, 'render', 'form_submit' );
    },
    render: function( response ) {
      var template = _.template( $('#new-book-template').html(), { 'response': response } );
      $('.content').html( $(this.el).html( template ) );
      
      $('.author').autocomplete({
        serviceUrl: 'author/find/'
      });      
      var bind_author_focus = function( el ) {
        var on_keypress = function() {
          $('.authors').append('<input type="text" name="author" class="author" placeholder="Author">');
          var new_field = $('.author').eq($('.author').length - 1);
          new_field.autocomplete({
            serviceUrl: 'author/find/'
          });
          el.off( 'keypress', on_keypress );
        }
        el.on( 'keypress', on_keypress );
      }
      bind_author_focus( $('.author') );
      
      
      $('.tag').autocomplete({
        serviceUrl: 'tag/find/'
      });
      var bind_tag_focus = function( el ) {
        var on_keypress = function() {
          $('.tags').append('<input type="text" name="tag" class="tag" placeholder="Tag">');
          var new_field = $('.tag').eq($('.tag').length - 1);
          new_field.autocomplete({
            serviceUrl: 'tag/find/'
          });
          bind_tag_focus( new_field );
          el.off( 'keypress', on_keypress );
        }
        el.on( 'keypress', on_keypress );
      }
      bind_tag_focus( $('.tag') );
      
      
    },
    form_submit: function() {
      var that = this;
      var tags = [];
      $('.tag').each( function() {
        if( $(this).val() != "" ) {
          tags.push( $(this).val() );
        }
      });
      authors = []
      $('.author').each( function() {
        if( $(this).val() != "" ) {
          authors.push( $(this).val() );
        }
      });
      $.ajax({
        data: {
          title: $(this.el).find( '.title' ).val(),
          color: $(this.el).find( '.color' ).val(),
          completed: $(this.el).find( '.completed' ).val(),
          checked_out: $(this.el).find( '.checked_out' ).val(),
          authors: authors,
          tags: tags
        },
        type: 'post',
        url: 'book/create/',
        success: function( data ) {
          data = JSON.parse( data );
          if( data['response'] == false ) {
            Backbone.history.navigate( '#', true );
          } else {
            var new_book_view = new window.NewBookView();
            new_book_view.render( data['response'] );
          }
        }
      });
      return false;
    }
  });
});