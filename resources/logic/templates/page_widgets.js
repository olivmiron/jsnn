// Neural network initialization

// var neural_network = {
//     name: "", 
//
//     initialization_type: "",
//
//     initialized: false, 
//
//     input_horizontal: 5,
//     input_vertical: 3,
//
//     output_horizontal: 3, 
//     output_vertical: 3,
//
//     deep_layers: 0, 
//     neurons_per_deep_layer: 0
// }


function initialize_network() {
    if(neural_network.initialized) {updates_console_update("Neural network already initialized", true /* error */);return 0;}
    
    neural_network.input_vertical = parseInt(document.getElementById("nn-initialization_input_size_vertical").value);
    neural_network.input_horizontal = parseInt(document.getElementById("nn-initialization_input_size_hotizontal").value);
    neural_network.output_vertical = parseInt(document.getElementById("nn-initialization_output_size_vertical").value);
    neural_network.output_horizontal = parseInt(document.getElementById("nn-initialization_output_size_hotizontal").value);
    neural_network.deep_layers = parseInt(document.getElementById("nn-initialization_deep_size_hotizontal").value);
    neural_network.neurons_per_deep_layer = parseInt(document.getElementById("nn-initialization_deep_size_vertical").value);
    
    neural_network.initialization_type = document.getElementById("nn_weight_initialization").getAttribute("data-value");

    neural_network.name = document.getElementById("nn_initialization_name").value.trim();

    neural_network.initialized = true;

    
    updates_console_update("Neural network initialized successfully", false);

    send_network_initialization();
}

function send_network_initialization() {
    // Send AJAX request to nn_initializer.php
    fetch('/resources/logic/back_end/nn_back_end_logic/initialization/nn_initializer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(neural_network)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            updates_console_update("Neural network initialized successfully", false);
        } else {
            neural_network.initialized = false;
            updates_console_update("Failed to initialize neural network: " + data.message, true);
        }
    })
    .catch(error => {
        neural_network.initialized = false;
        updates_console_update("Error initializing neural network: " + error, true);
    });
}



// Image painter

var image_painter_painting_started = false;var the_initial_event;

document.getElementById("image_painter_input_squares_container").addEventListener("mousedown", (e) => {toggle_image_painting(e, true)} );
document.getElementById("image_painter_output_squares_container").addEventListener("mousedown", (e) => {toggle_image_painting(e, true)} );

document.getElementById("image_painter_input_squares_container").addEventListener("contextmenu", (e) => {e.preventDefault();});
document.getElementById("image_painter_output_squares_container").addEventListener("contextmenu", (e) => {e.preventDefault();});

document.addEventListener("mouseup", (e) => {toggle_image_painting(e, false)});

function toggle_image_painting(the_event, value) {the_initial_event = the_event;image_painter_painting_started = value;}


document.getElementById("image_painter_input_squares_container").addEventListener("mouseover", (e) => {image_painter_toggle_squares(e);});
document.getElementById("image_painter_output_squares_container").addEventListener("mouseover", (e) => {image_painter_toggle_squares(e);});

function image_painter_toggle_squares(the_event) {
    if(!image_painter_painting_started) {return 0;}

    if(the_event.target.classList.contains("image_painter_paint_square")) {
        if(the_initial_event.button === 0) {the_event.target.classList.add("image_painter_paint_square_active");}
        else if(the_initial_event.button === 2) {event.preventDefault();the_event.target.classList.remove("image_painter_paint_square_active");}
    }
}


var training_images = [];

function image_painter_save_image() {
    //insert into training_images_super_array

    var current_training_images_number = training_images.length;

    var training_image_input = [], training_image_output = [];

    document.getElementById("image_painter_input_squares_container").querySelectorAll(".image_painter_paint_square").forEach(element => {
        training_image_input.push((element.classList.contains("image_painter_paint_square_active") ? 1 : 0));
    });
    
    document.getElementById("image_painter_output_squares_container").querySelectorAll(".image_painter_paint_square").forEach(element => {
        training_image_output.push((element.classList.contains("image_painter_paint_square_active") ? 1 : 0));
    });

    training_images.push([training_image_input, training_image_output]);

    
    
    //insert inside training images widget

    var training_image_template = document.getElementById("training_image_template").content.cloneNode(true);
    var training_image_square_template = document.getElementById("training_image_square_template");
    var training_images_container = document.getElementById("training_images_container");

    var training_image_template_input = training_image_template.querySelector(".training_image_input");

    training_image_input.forEach((element, index) => {
        var training_image_square_template_element = training_image_square_template.content.cloneNode(true);
        if(element != 1) {training_image_square_template_element.querySelector(".training_image_paint_square").classList.remove("training_image_paint_square_active");}
        training_image_template_input.appendChild(training_image_square_template_element);

        if(index > 0 && (index + 1) % neural_network.input_horizontal == 0) {training_image_template_input.appendChild(document.createElement("br"));}
    });

    
    var training_image_template_input = training_image_template.querySelector(".training_image_output");
    
    training_image_output.forEach((element, index) => {
        var training_image_square_template_element = training_image_square_template.content.cloneNode(true);
        if(element != 1) {training_image_square_template_element.querySelector(".training_image_paint_square").classList.remove("training_image_paint_square_active");}
        training_image_template_input.appendChild(training_image_square_template_element);

        if(index > 0 && (index + 1) % neural_network.output_horizontal == 0) {training_image_template_input.appendChild(document.createElement("br"));}
    });


    training_images_container.appendChild(training_image_template);
}



// updates console

function updates_console_update(message, error_or_not) {
    var updates_console_log_element = document.getElementById("updates_console_item_template").content.cloneNode(true);
    var updates_console_logs_container = document.getElementById("nn_console");


    const now = new Date();
    
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const year = String(now.getFullYear()).slice(-2);
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    updates_console_log_element.querySelector(".nn_console_item_div_timedate").innerText = `${day}/${month}/${year} ${hours}:${minutes}`;


    updates_console_log_element.querySelector(".nn_console_item_div_content").innerText = message;

    if(error_or_not) {updates_console_log_element.querySelector(".nn_console_item_div").classList.add("nn_console_item_div_error");}

    updates_console_logs_container.appendChild(updates_console_log_element);

}