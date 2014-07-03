// wait for page to load
$(document).ready(function()
{
  // variables
  var query_count = 0;  // for keeping track of 
  var currentlyScrolling = false;  // for limiting Ajax requests

  // wait for form to be submitted
  $("#form").on("submit", function()
  {
    //error check
    $("#output").hide();
    $("#info_box").hide();
    if ($("#ranking_scheme").val() === "default")
      document.getElementById("hint").innerHTML = "Please select an option from the drop down menu labeled <i>Rank Classes By</i> to search!</br>";
    else if ($("#fields_of_study").val() === "default")
      document.getElementById("hint").innerHTML = "Please select an option from the drop down menu labeled <i>Fields of Study</i> to search!</br>";
    else if ($("#display").val() === "default")
      document.getElementById("hint").innerHTML = "Please select an option from the drop down menu labeled <i>Order</i> to search!</br>";
    else if ($("#terms").val() === "default")
      document.getElementById("hint").innerHTML = "Please select an option from the drop down menu labeled <i>Terms</i> to search!</br>";
    else if ($("#data_type").val() === "default")
      document.getElementById("hint").innerHTML = "Please select an option from the drop down menu labeled <i>Data</i> to search!</br>";
    // query
    else
    {
      // reset query count
      query_count = 0;

      // put up loading sign
        $("#hint").html("Loading...");
        $("#info_box").hide();
        $("#output").hide();

      // store inputs
      var ranking_scheme = $("#ranking_scheme").val();
      var fields_of_study = $("#fields_of_study").val();
      var display = $("#display").val();
      var terms = $("#terms").val();
      var data_type = $("#data_type").val();

      // ajax post request
      $.post("php/backend.php", {ranking_scheme: ranking_scheme, fields_of_study: fields_of_study, display: display, 
      terms: terms, data_type: data_type, query_count: query_count}, function(data)
      {
        // column titles
        var table_header = "\
                  <table id='upper_table'>\
                    <tr>\
                                    <td class='upper_table_row' id='ranking'>Rank</td>\
                                    <td class='upper_table_row' id='number'>Course Number</td>\
                                    <td class='upper_table_row' id='title'>Title</td>\
                                    <td class='upper_table_row' id='catalog'>Catalog #</td>\
                                    <td class='upper_table_row' id='overall'>Overall Q</td>\
                                    <td class='upper_table_row' id='difficulty'>Difficulty</td>\
                                    <td class='upper_table_row' id='workload'>Workload</td>\
                                    <td class='upper_table_row' id='recommendations'>Peer Rec.</td>\
                                 </tr>\
                             </table>";

        // output data
        $("#hint").html(table_header);
        $("#output").html(data);
        $("#output").show();

        // update query count 
        if (data.indexOf("<tr height='40'><td colspan='8'>No [more] data to show, try searching again!</td></tr>") != -1)
          query_count = 0;
        else
          query_count++;
      });
    }

    // stop loading of new page
    return false;
  });

  // add more data
  $(window).scroll(function()
  {
    // limit ajax request if need be
    if (currentlyScrolling === true)
      return;

    // append more data if user has scrolled 3/4ths of the page
    if (($(window).scrollTop() >= ((3/4) * $(document).height() - $(window).height())) && query_count != 0)
    {
      // grab Ajax lock
      currentlyScrolling = true;

      // store inputs
      var ranking_scheme = $("#ranking_scheme").val();
      var fields_of_study = $("#fields_of_study").val();
      var display = $("#display").val();
      var terms = $("#terms").val();
      var data_type = $("#data_type").val();

      // ajax post request
      $.post("php/backend.php", {ranking_scheme: ranking_scheme, fields_of_study: fields_of_study, display: display, 
      terms: terms, data_type: data_type, query_count: query_count}, function(data)
      {
        // case 1: only useful data is returned
        if (data.indexOf("<tr height='40'><td colspan='8'>No [more] data to show, try searching again!</td></tr>") == -1)
        {
          $("#output").append(data);
          query_count++;
          currentlyScrolling = false;  // release Ajax lock
        }
        // end of data is reached somewhere
        else
        {
          // append data
          $("#output").append(data);
          query_count = 0;
          currentlyScrolling = false;  // release Ajax lock
        }
      });
    }
  });
});