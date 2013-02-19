$(document).ready( function() {
  
  window.Status = Backbone.Model.extend({
    url: 'status/'
  })
  
  window.AppView = Backbone.View.extend({
    className: 'page',
    align_menu: function() {
      var primary_lis = $('nav').children('ul').children('li');
      var all_lis = $('nav').find('li');
      all_lis.width( primary_lis.parent().width() / primary_lis.length );
      var clickable_lis = primary_lis.find('ul').parent();
      clickable_lis.each( function() {
        $(this).click( function() {
          $(this).children('ul').slideToggle( 100 );
        });
      });
    },
    render: function() {
      var that = this;
      if( this.model.get('is_logged_in') ) {
        var template = _.template( $('#app-template').html(), {
          'response': true,
          'username': this.model.get('username'),
          'api_token': this.model.get('api_token')
        });        
      } else {
        var template = _.template( $('#app-template').html(), { 'response': false } );
      }
      $('.page-wrapper').html( $(that.el).html( template ) );
      that.align_menu();
    }
  });
  
});