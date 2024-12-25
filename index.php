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
        <div id="index_top_settings">
            <span>Widgets per row: </span>
            <input type="number" value="2" onclick="this.select();" oninput="index_change_widgets_per_row(this.value);">

            <span>Show/hide widgets: </span>

            <div class="checkbox checkbox_checked" onclick="toggle_checkbox(this);toggle_widgets('initialize_neural_network')">
                <div class="checkbox_box"><img src="/resources/design/media/icons/check_mark.png"/></div>
                <div class="checkbox_text">
                    <span>Initialize neural network</span>
                </div>
            </div>

            <div class="checkbox checkbox_checked" onclick="toggle_checkbox(this);toggle_widgets('weights_display')">
                <div class="checkbox_box"><img src="/resources/design/media/icons/check_mark.png"/></div>
                <div class="checkbox_text">
                    <span>Weights display</span>
                </div>
            </div>

            <div class="checkbox checkbox_checked" onclick="toggle_checkbox(this);toggle_widgets('image_painter')">
                <div class="checkbox_box"><img src="/resources/design/media/icons/check_mark.png"/></div>
                <div class="checkbox_text">
                    <span>Image painter</span>
                </div>
            </div>

            <div class="checkbox checkbox_checked" onclick="toggle_checkbox(this);toggle_widgets('training_images')">
                <div class="checkbox_box"><img src="/resources/design/media/icons/check_mark.png"/></div>
                <div class="checkbox_text">
                    <span>Training images</span>
                </div>
            </div>

            <div class="checkbox checkbox_checked" onclick="toggle_checkbox(this);toggle_widgets('training_tab')">
                <div class="checkbox_box"><img src="/resources/design/media/icons/check_mark.png"/></div>
                <div class="checkbox_text">
                    <span>Training tab</span>
                </div>
            </div>

            <div class="checkbox checkbox_checked" onclick="toggle_checkbox(this);toggle_widgets('updates_console')">
                <div class="checkbox_box"><img src="/resources/design/media/icons/check_mark.png"/></div>
                <div class="checkbox_text">
                    <span>Updates console</span>
                </div>
            </div>
            
        </div>

        <?php echo complex_template_iterator("/resources/logic/templates/page_widget/"); ?>

    </body>
</html>