<?php

    if (isset($_POST["ranking_scheme"], $_POST["fields_of_study"], $_POST["display"], $_POST["terms"], $_POST["query_count"]))
    {
        // MySQL and POST variables
        $link = mysql_connect('fdb6.awardspace.net:3306','1485284_courses','sortclasses123');
        $db_selected = mysql_select_db("1485284_courses", $link);
        $ranking_scheme = $_POST["ranking_scheme"];
        $fields_of_study = $_POST["fields_of_study"];
        $display = $_POST["display"];
        $terms = $_POST["terms"];
        $data_type = $_POST["data_type"];

        // get limits for MySQL query
        $query_count_min = 0;
        $query_count_max = 50;
        if ($_POST["query_count"] == 0) // if new query, reset table count
        {
            $count_query = "UPDATE `CustomQCount` SET `Count` = 0";
            mysql_query($count_query, $link);
        }
        else 
        {
            // get correct limits for MySQL query
            $count_query = "SELECT `Count` FROM `CustomQCount`";
            $count_data = mysql_query($count_query, $link);
            $count_array = array();
            while ($row = mysql_fetch_assoc($count_data))
                $count_array[] = $row;
            $query_count_min = $count_array[0]["Count"];
        }
       
        // adjust terms
        $term = $terms;
        $terms = ($terms == "both") ? 3 : (($terms == "fall") ? 1 : 2);

        // adjust $data_type for MySQL data
        $data_type = ($data_type == "most_recent") ? "CustomQMostRecent" : "CustomQAverage";

        // set up query elements
        $where_query_string = ($fields_of_study == "ALL") ? "" : "WHERE `number` LIKE \"%$fields_of_study%\"";
        $order_by_query_string = "";
        $order_by_query_string = 'ORDER BY `'.$ranking_scheme.'`';
        $from_query_string = 'FROM `'.$data_type.'`';
        $order_by_query_string .= ($display == "lowest") ? " ASC" : " DESC";

        // adjust for querying by course numbers and query
        if ($ranking_scheme == "number")
        {
            $order_by_query_string = 'ORDER BY `prefix`';
            $order_by_query_string .= ($display == "lowest") ? " ASC" : " DESC";
            $order_by_query_string .= ', `suffix`';
            $order_by_query_string .= ($display == "lowest") ? " ASC" : " DESC";
        }
        $query = "
        SELECT *
        $from_query_string
        $where_query_string
        $order_by_query_string
        LIMIT $query_count_min, $query_count_max";
        $mysql_data = mysql_query($query, $link);
        $raw_data = array();
        while ($class = mysql_fetch_assoc($mysql_data))
            $raw_data[] = $class;

        // replace NULL fields of classes
        $spot = 0;
        for ($i = 0; $i < count($raw_data); $i++)
        {
            $class = $raw_data[$i];
            $updated = 0;
            if ($class["title"] == NULL || $class["title"] == "N/A")
            {
                $class["title"] = "N/A";
                $updated = 1;
            }
            if ($class["cat_num"] == NULL)
            {
                $class["cat_num"] = "--";
                $updated = 1;
            }
            if ($class["CourseOverall"] == NULL)
            {
                $class["CourseOverall"] = "--";
                $updated = 1;
            }
            if ($class["Difficulty"] == NULL)
            {
                $class["Difficulty"] = "--";
                $updated = 1;
            }
            if ($class["Workload"] == NULL)
            {
                $class["Workload"] = "--";
                $updated = 1;
            }
            if ($class["WouldYouRecommend"] == NULL)
            {
                $class["WouldYouRecommend"] = "--";
                $updated = 1;
            }

            if ($updated == 1)
                $raw_data[$i] = $class;
        }

        // print all class data
        echo "<table id='lower_table'>";
        $ranking = ($query_count_min == 0) ? 1 : ($query_count_min + 1); 
        $empty = 1;
        foreach ($raw_data as $class)
        {
            // make sure some data is present to show
            if ($empty == 1)
                $empty = 0;

            // skip classes if not available in selected term
            if ($class["term"] != $terms && $terms != 3)
            {
                if ($class["term"] != 3)
                    continue;
            }

            echo "<tr>" . 
                    "<td class='lower_table_row' id='ranking'>" . $ranking . "</td>" .
                    "<td class='lower_table_row' id='number'>" . "<a href=\"php/classinfo.php?cat_num=" . $class["cat_num"] . "\">" . $class["number"] . "</a></td>" .
                    "<td class='lower_table_row' id='title'>" . $class["title"] . "</td>" .
                    "<td class='lower_table_row' id='catalog'>" . $class["cat_num"] . "</td>" .
                    "<td class='lower_table_row' id='overall'>" . $class["CourseOverall"] . "</td>" .
                    "<td class='lower_table_row' id='difficulty'>" . $class["Difficulty"] . "</td>" .
                    "<td class='lower_table_row' id='workload'>" . $class["Workload"] . "</td>" .
                    "<td class='lower_table_row' id='recommendations'>" . $class["WouldYouRecommend"] . "</td>" .
                 "</tr>";
            $ranking++;
        }
        // print message if no data, or no more data, is present to show
        if ($empty == 1 || (($ranking - 1) % 50 != 0))
            echo "<tr height='40'><td colspan='8'>No [more] data to show, try searching again!</td></tr>";

        // update count for next query
        $count_entry = $ranking - 1;
        $update_query = "UPDATE `CustomQCount` SET `Count` = $count_entry";
        mysql_query($update_query, $link);
    }
    else
        echo "Unable to get data. Hmm maybe the administration took us down...\n";
?>