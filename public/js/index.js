$(document).ready(function(){

  let url = '';
  let tag = '';

  $('#urlSearchInput').keyup(function(){

    url = $(this).val();

  }); // keyup urlSearchInput

  $('#tagSearchInput').keyup(function () {

    tag = $(this).val();

  });

  $('#submitButton').click(function(){

    if(url === '' && tag === ''){

      $('#textForInput').html('Please fill in the fields');
      $('#urlSearchInput').addClass('alert alert-warning');
      $('#tagSearchInput').addClass('alert alert-warning');

      return 0;

    } // if

    if(url === ''){

      $('#textForInput').html('Please, enter URL');
      $('#urlSearchInput').addClass('alert alert-warning');

      return 0;

    } // if

    if(tag === ''){

      $('#textForInput').html('Please, enter tag');
      $('#tagSearchInput').addClass('alert alert-warning');

      return 0;

    } // if

    $.post({

      url: './',
      data: { searchUrl: url, tag: tag },
      beforeSend: function(msg) {

        $('#loader').removeClass('hideElement').addClass('searchLoader');

        if(!$('#searchResult').hasClass('hideElement')){
          $('#searchResult').addClass('hideElement');
        } // if

      }, // beforeSend
      success: function(msg){

        $('#loader').removeClass('searchLoader').addClass('hideElement');
        $('#searchResult').removeClass('hideElement');

        // Parse JSON data of PHP file
        var arrayData = JSON.parse(msg);

        if(arrayData.invalidUrl) {

          $('#textForInput').html('Invalid URL address, please try again');
          $('#urlSearchInput').addClass('alert alert-warning');

          if(!$('#searchResult').hasClass('hideElement')){
            $('#searchResult').addClass('hideElement');
          } // if

          return 0;

        } // if

        if(arrayData.invalidTag){

          $('#textForInput').html('Invalid HTML tag, please try again');
          $('#tagSearchInput').addClass('alert alert-warning');

          if(!$('#searchResult').hasClass('hideElement')){
            $('#searchResult').addClass('hideElement');
          } // if

          return 0;

        } // if

          $('#textForInput').html('URL checked');

          $('#urlSearchInput').removeClass('alert alert-warning');
          $('#tagSearchInput').removeClass('alert alert-warning');

          if(arrayData.countImg === 'empty'){
            $('#p_img').html('This URL not have &lt;' + arrayData.checkedElement + '&gt; elements!');
          } // if
          else{
            $('#p_img').html('Element &lt;' + arrayData.checkedElement + '&gt; appeared ' + arrayData.countImg + ' times in page.');
          } // else

          $('#p_url').html('URL: ' + arrayData.siteUrl);
          $('#p_fetch').html('Fetched: on ' + arrayData.fetched);
          $('#p_took').html('Took: ' + arrayData.timeTook + ' sec.');

          $('#p_mySql_countURL').html(
              arrayData.countUrl + ' different URLs from ' + arrayData.domain + ' have been fetched'
          ); // p_mySql_countURL

          $('#p_mySql_countElements').html(
              'There was a total of ' + arrayData.countElements + ' &lt;' +
              arrayData.checkedElement + '&gt; elements from ' + arrayData.domain
          ); // p_mySql_countElements

          $('#p_mySql_countAllElements').html('Total of ' + arrayData.countAllElements +
              ' &lt;'+ arrayData.checkedElement +'&gt; elements counted in all requests ever made.'
          ); // p_mySql_countAllElements

          $('#p_mySql_countDuration').html('Average fetch time from ' + arrayData.domain +
              ' during the last 24 hours hours is ' + arrayData.countDuration + ' sec.'
          ); // p_mySql_countDuration

      }, // success
      error: function(er){
          
        return 'Error: ' + er.message();

      }, // error

    }); // ajax

  }); // click submitButton

}); // document
