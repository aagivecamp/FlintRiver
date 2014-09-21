var fb_types = new Array();
fb_types['rss'] = new Array('url');
fb_types['photostream'] = new Array('api_key', 'account', 'tags');
fb_types['sets'] = new Array('api_key', 'account', 'sets');
fb_types['favorites'] = new Array('api_key', 'account');

var fb_fields = new Array('url', 'api_key', 'account', 'sets', 'tags');

function selectType(newType, id) {

   // Hide all fields
   for ( var field in fb_fields ) {
      jQuery('#flickrpress_'+id+'_container_'+fb_fields[field]).hide();
   }
   
   // Show relevant fields
   for ( var field in fb_types[newType] ) {
      jQuery('#flickrpress_'+id+'_container_'+fb_types[newType][field]).show();
   }
   
}
