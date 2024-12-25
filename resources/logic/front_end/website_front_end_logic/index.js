// root variables

var neural_network = {
    name: "", 
    initialization_type: "",
    initialized: false, 
    input_horizontal: 5,
    input_vertical: 3,
    output_horizontal: 3, 
    output_vertical: 3,
    deep_layers: 0, 
    neurons_per_deep_layer: 0
}

// index_settings

function index_change_widgets_per_row(widgets_per_row) {
    document.querySelector(':root').style.setProperty("--widgets_per_row", widgets_per_row);
    console.log(widgets_per_row);
}

function toggle_widgets(widget_to_toggle) {
    var widget = document.getElementById("widget_" + widget_to_toggle).closest(".page_widget");
    if (widget.style.display === "none") {
        widget.style.display = "inline-block";
    } else {
        widget.style.display = "none";
    }
}