jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Index.initCalendar(); // init index page's custom scripts
   TableAdvanced.init();
   UINestable.init();
   UIAlertDialogApi.init();
   FormValidation.init();
   ComponentsPickers.init();
   FormEditable.init();
   Login.init();
   if (! $('.page-sidebar-wrapper').length) { $('body').addClass('page-sidebar-closed'); }
});