<?php

    // remove error messages
  error_reporting(E_ERROR);

  /*
   *   Get supplementary course data from table CustomQClassData
   */
  // query MySQL
  $cat_num = $_GET["cat_num"];
  $link = mysql_connect('fdb6.awardspace.net:3306','1485284_courses','sortclasses123');
    $db_selected = mysql_select_db("1485284_courses", $link);
  $query = "SELECT * FROM `CustomQClassData` WHERE `cat_num` = $cat_num";
  $mysql_data = mysql_query(mysql_real_escape_string($query), $link);
  $raw_data = array();
  while ($class = mysql_fetch_assoc($mysql_data))
    $raw_data[] = $class;
  $load_page = (count($raw_data) > 0) ? 1 : 0;

  // echo error message if no data
  if ($load_page == 0)
  {
    echo "
    <!DOCTYPE html>  
      <html>
        <head>
          <!-- browser tab image -->
          <link rel='shortcut icon' href='../img/title_logo.png' type='image/png'>
          <!-- CSS -->
          <link href='../css/classinfostyles.css' rel='stylesheet'>
          <title>Harvard Class Finder - 404</title>
        </head>
        <body>
          <!-- red logo atop page -->
          <div id='logo'>
            <a href='../index.html'><img src='../img/logo_6.png' width='1000' height='100'></a>
          </div>
          <!-- description under logo -->
          <div id='box'>
            [ Page Not Found ]
          </div>
          <!-- links to other class related website -->
          <div>
            <table id='links' align='center'>
              <tr>
                <td>Useful links:</td>
                <td><a href='../index.html'>Home</a></td>
                <td><a href='../faq.html'>About / FAQ</a></td>
                <td><a href='http://www.my.harvard.edu'>My Harvard</a></td>
                        <td><a href='https://webapps.fas.harvard.edu/course_evaluation_reports/fas/list?'>Harvard Q</a></td>
                        <td><a href='https://courses.cs50.net/'>CS50 Courses</a></td>
                       </tr>
            </table>
          </div></br>
          <table class='headers'>
            <tr>
              <td>Error</td>
            </tr>
          </table>
          <div id='error_message'>
            <h2 id='error_course_number'>[ 404 ]</h2></br>
            <p class='error_words'>Oops, you have encountered an error. The requested page was not found on this server. Please try again.</p>
          </div>";
  }
  // else show class data
  else
  {
    // handle different cases; load current semester if available, else load data from only available semester
    if (count($raw_data) > 1)
      $course = (floatval($raw_data[0]["year"]) > floatval($raw_data[1]["year"])) ? $raw_data[0] : $raw_data[1];
    else
      $course = $raw_data[0];

    // class data variables
    $title = ($load_page == 0) ? "--" : $course["title"];
    $cat_num = $course["cat_num"];
    $instructor = $course["instructor"];
    $prerequisites = ($course["prerequisites"] == "N/A") ? "None" : $course["prerequisites"];  // adjust for N/A data
    $meetings = ($course["meetings"] == "N/A") ? "Hours to be arranged" : $course["meetings"];  // adjust for N/A data
    $building = ($course["building"] == "N/A") ? "Building to be arranged" : $course["building"];  // adjust for N/A data
    $room = ($course["room"] == "N/A") ? "Room to be arranged" : $course["room"];  // adjust for N/A data
    $description = ($course["description"] == "N/A") ? "No description available" : $course["description"];  // adjust for N/A
    $notes = ($course["notes"] == "N/A") ? "No notes" : $course["notes"];  // adjust for N/A data

    /*
     *   Get q course data from CustomQMostRecent
     */
    // query MySQL
    $q_query = "SELECT * FROM `CustomQMostRecent` WHERE `cat_num` = $cat_num";
    $q_mysql_data = mysql_query(mysql_real_escape_string($q_query), $link);
    $q_raw_data = array();
    while ($q_class = mysql_fetch_assoc($q_mysql_data))
      $q_raw_data[] = $q_class;
    $q_course = $q_raw_data[0];

    // class q data variables
    $number = $q_course["number"];
    $prefix = $q_course["prefix"];  // use this to get the department
    $suffix = $q_course["suffix"];
    $term = ($q_course["term"] == 1) ? "Fall" : (($q_course["term"] == 2) ? "Spring" : "Spring, Fall");    // convert number to term string
    $year = $q_course["year"];
    $evaluations = ($q_course["Evaluations"] == NULL) ? "N/A" : $q_course["Evaluations"];
    $course_overall = ($q_course["CourseOverall"] == NULL) ? "N/A" : $q_course["CourseOverall"];
    $workload = ($q_course["Workload"] == NULL) ? "N/A" : $q_course["Workload"];
    $difficulty = ($q_course["Difficulty"] == NULL) ? "N/A" : $q_course["Difficulty"];
    $recommendation = ($q_course["WouldYouRecommend"] == NULL) ? "N/A" : $q_course["WouldYouRecommend"];
    
    echo "
      <!DOCTYPE html>  
      <html>
        <head>
          <!-- browser tab image -->
          <link rel='shortcut icon' href='../img/title_logo.png' type='image/png'>
          <!-- CSS -->
          <link href='../css/classinfostyles.css' rel='stylesheet'>
          <title>Harvard Class Finder - $number</title>
        </head>
        <body>
          <!-- red logo atop page -->
          <div id='logo'>
            <a href='../index.html'><img src='../img/logo_6.png' width='1000' height='100'></a>
          </div>
          <!-- description under logo -->
          <div id='box'>
            [ Course Information for $number ]
          </div>
          <!-- links to other class related website -->
          <div>
            <table id='links' align='center'>
              <tr>
                <td>Useful links:</td>
                <td><a href='../index.html'>Home</a></td>
                <td><a href='../faq.html'>About / FAQ</a></td>
                <td><a href='http://www.my.harvard.edu'>My Harvard</a></td>
                        <td><a href='https://webapps.fas.harvard.edu/course_evaluation_reports/fas/list?'>Harvard Q</a></td>
                        <td><a href='https://courses.cs50.net/'>CS50 Courses</a></td>
                       </tr>
            </table>
          </div>
          <!-- box with course number -->
          <table class='headers'>
            <tr>
              <td>[ Course ]</td>
            </tr>
          </table>
          <div id='course_number_box'>
            <h2 id='course_number'>$number</h2>
            <p id='course_title'>[ $title ]</p>
          </div></br>
          <!-- box with 4 major rating attributes -->
          <table class='headers'>
            <tr>
              <td>[ Scores ]</td>
            </tr>
          </table>
          <table id='course_scores_table_headers'>
            <tr>
              <td id='catalog_number' class='course_score_table_headers_entry' style='border-left: 1px solid black;'>Catalog #</td>
              <td id='overall_q' class='course_score_table_headers_entry'>Overall Q</td>
              <td id='difficulty' class='course_score_table_headers_entry'>Difficulty</td>
              <td id='workload' class='course_score_table_headers_entry'>Workload</td>
              <td id='recommendation' class='course_score_table_headers_entry' style='border-right: 1px solid black'>Peer Rec.</td>
            </tr>
          </table>
          <table id='course_scores'>
            <tr>
              <td id='catalog_number_score' class='course_score_table_entry'>$cat_num</td>
              <td id='overall_q_score' class='course_score_table_entry'>$course_overall</td>
              <td id='difficulty_score' class='course_score_table_entry'>$difficulty</td>
              <td id='workload_score' class='course_score_table_entry'>$workload</td>
              <td id='recommendation_score' class='course_score_table_entry'>$recommendation</td>
            </tr>
          </table></br>                    
          <!-- box with other info for course -->
          <table class='headers'>
            <tr>
              <td>[ Prof. and Evals]</td>
            </tr>  
          </table>
          <table id='other_course_info_header'>
            <tr>
              <td id='course_instructor_header' class='other_course_info_header'>Instructor</td>
              <td id='course_student_responses' class='other_course_info_header'>Q Evaluations</td>
            </tr>
          </table>
          <table id='other_course_info'>
            <tr>
              <td id='other_course_info_instructor' class='other_course_info_entry'>$instructor</td>
              <td id='other_course_info_responses' class='other_course_info_entry'>$evaluations</td>
            </tr>
          </table></br>
          <!--  box with other course info -->
          <table class='headers'>
            <tr>
              <td>[ Other Info ]</td>
            </tr>
          </table>
          <table id='misc_info'>
            <tr>
              <td class='left_misc_entry'>Prerequisites</td>
              <td class='right_misc_entry'>$prerequisites</td>
            </tr>
            <tr>
              <td class='left_misc_entry'>Meeting Time(s)</td>
              <td class='right_misc_entry'>$meetings</td>
            </tr>
            <tr>
              <td class='left_misc_entry'>Terms</td>
              <td class='right_misc_entry'>$term</td>
            </tr>
            <tr>
              <td class='left_misc_entry'>Building</td>
              <td class='right_misc_entry'>$building</td>
            </tr>
            <tr>
              <td class='left_misc_entry' style='border-bottom: 1px solid black'>Room</td>
              <td class='right_misc_entry'>$room</td>
            </tr>
          </table></br>
          <!--  box with course description -->
          <table class='headers'>
            <tr>
              <td>[ Description ]</td>
            </tr>
          </table>
          <div id='course_description_box'>
            <p id='course_description'>$description</p>
          </div></br>
          <!--  box with course notes -->
          <table class='headers'>
            <tr>
              <td>[ Notes ]</td>
            </tr>
          </table>
          <div id='course_notes_box'>
            <p id='course_notes'>$notes</p>
          </div></br>
          <div id='footer'>
            <p>Nicholas Mahlangu</br>Theharvardclassfinder &#169 2014</p>
          </div>
        </body>
      </html>";
  }
?>