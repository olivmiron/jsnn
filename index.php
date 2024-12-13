<?php require ($_SERVER['DOCUMENT_ROOT'] . "/resources/logic/back_end/website_back_end_logic/complex_templates_iterator.php"); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Javascript Neural Network</title>

        <link rel="stylesheet" href="/resources/design/stylesheets/index.css">
        <link rel="icon" type="image/x-icon" href="/resources/design/other/favicon.ico"/>
        <script src="/resources/logic/front_end/website_front_end_logic/global.js"></script>
        <script src="/resources/logic/front_end/website_front_end_logic/index.js"></script>

        <script src="/resources/logic/templates/page_widgets.js" defer></script>
    </head>
    <body>
        <div id="index_widgets_per_row_div">
            <span>Widgets per row: </span>
            <input type="number" value="2" onclick="this.select();" oninput="index_change_widgets_per_row(this.value);">
        </div>

        <?php echo complex_template_iterator("/resources/logic/templates/page_widget/"); ?>

        <div style="display: none;">

            <input type="text" placeholder="name" onclick="this.select();">

            <input type="number" value="12" onclick="this.select();">

            <div class="dropdown" data-opened="0" data-value="1" onclick="dropdown_toggle(this)">
                <div class="dropdown_main">
                    <span class="dropdown_main_text">Selection #1</span>
                    <div class="dropdown_main_arrow">
                        <img src="/resources/design/media/icons/arrow_down_black.png"/>
                    </div>
                </div>
                <div class="dropdown_list">
                    <div class="dropdown_list_element dropdown_list_active_element" data-value="1" onclick="dropdown_select(this)"><span>Selection #1</span></div>
                    <div class="dropdown_list_element" data-value="2" onclick="dropdown_select(this)"><span>Selection #2</span></div>
                    <div class="dropdown_list_element" data-value="3" onclick="dropdown_select(this)"><span>Selection #3</span></div>
                    <div class="dropdown_list_element" data-value="4" onclick="dropdown_select(this)"><span>Selection #4</span></div>
                </div>
            </div>

            <div class="checkbox checkbox_checked" onclick="toggle_checkbox(this)">
                <div class="checkbox_box"><img src="/resources/design/media/icons/check_mark.png"/></div>
                <div class="checkbox_text">
                    <span>Check mark</span>
                </div>
            </div>

            <div class="button">Click</div>

        </div>

    </body>
</html>