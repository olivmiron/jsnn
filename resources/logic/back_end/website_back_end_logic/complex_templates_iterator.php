<?php

function complex_template_iterator($complex_widget_path) {
    $widgets_container = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $complex_widget_path . "container.html"); 
    $page_widget = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $complex_widget_path . "master.html"); 
    $page_widget_instructor = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $complex_widget_path . "instructor.json"));

    $html_content = "";

    for($i = 0; $i < intval($page_widget_instructor->iterations);$i++) {
        $replacer = [];

        for($j = 0; $j < count($page_widget_instructor->replacement_keys);$j++) {
            if($page_widget_instructor->replacement_types[$j] == "string") {
                array_push($replacer, $page_widget_instructor->replacements[$i]->{$page_widget_instructor->replacement_names[$j]});
            }
            elseif($page_widget_instructor->replacement_types[$j] == "html") {
                array_push($replacer, file_get_contents($_SERVER['DOCUMENT_ROOT'] . $complex_widget_path . "subtemplates/" . $page_widget_instructor->replacements[$i]->{$page_widget_instructor->replacement_names[$j]}));
            }
        }


        $html_content .= str_replace($page_widget_instructor->replacement_keys, $replacer, $page_widget);
    }


    $js_css = $page_widget_instructor->js_css;

    return str_replace(["##html_content##", "##js_css##"], [$html_content, $js_css], $widgets_container);
}

?>