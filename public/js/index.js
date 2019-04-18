$(document).ready(function(){

  let url = '';

  $('#urlSearchInput').keyup(function(){

    url = $(this).val();

  }); // keyup urlSearchInput

  $('#submitButton').click(function(){

    if(url === ''){

      $('#textForInput').html('Please, enter URL');
      $('#urlSearchInput').addClass('alert alert-warning');

    } // if

    $.post({

      url: './',
      data: 'searchUrl=' + url,
      beforeSend: function(msg) {

        $('#loader').removeClass('hideElement').addClass('searchLoader');

        if(!$('#searchResult').hasClass('hideElement')){
          $('#searchResult').addClass('hideElement');
        } // if

      }, // beforeSend
      success: function(msg){

        $('#loader').removeClass('searchLoader').addClass('hideElement');
        $('#searchResult').removeClass('hideElement');

        let arrayData = JSON.parse(msg);

        if(arrayData.invalid) {

          $('#textForInput').html('Invalid URL address, please try again');
          $('#urlSearchInput').addClass('alert alert-warning');

          if(!$('#searchResult').hasClass('hideElement')){
            $('#searchResult').addClass('hideElement');
          } // if

        } // if
        else {

          $('#textForInput').html('URL checked');
          $('#urlSearchInput').removeClass('alert alert-warning');

          if(arrayData.countImg === 'empty'){
            $('#p_img').html('This URL not have &lt;img&gt; elements!');
          } // if
          else{
            $('#p_img').html('Element &lt;img&gt; appeared ' + arrayData.countImg + ' times in page.');
          } // else

          $('#p_url').html('URL: ' + arrayData.siteUrl);
          $('#p_fetch').html('Fetched: on ' + arrayData.fetched);
          $('#p_took').html('Took: ' + arrayData.timeTook + ' sec.');

        } // else

      }, // success
      error: function(er){

        return 'Error: ' + er;

      }, // error

    }); // ajax

  }); // click submitButton

}); // document
