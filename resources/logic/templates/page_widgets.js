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
//     neurons_per_deep_layer: 0, 
// 
//     training_iterations: 0
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

    neural_network.training_iterations = 0;

    
    updates_console_update("Neural network initialized successfully", false);

    send_network_initialization();

    initialize_image_painter();
    empty_training_images();

    initialize_weights_display();
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
            neural_network.initialized = true;
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


function initialize_image_painter() {
    var image_painter_input_squares_container = document.getElementById("image_painter_input_squares_container");
    var image_painter_output_squares_container = document.getElementById("image_painter_output_squares_container");

    image_painter_input_squares_container.innerHTML = "";
    image_painter_output_squares_container.innerHTML = "";

    for(var i = 0; i < neural_network.input_horizontal * neural_network.input_vertical; i++) {
    if (i != 0 && i % neural_network.input_horizontal === 0) {image_painter_input_squares_container.appendChild(document.createElement("br"));}
        var image_painter_square_template = document.getElementById("image_painter_square_template").content.cloneNode(true);
        image_painter_input_squares_container.appendChild(image_painter_square_template);
    }

    for(var i = 0; i < neural_network.output_horizontal * neural_network.output_vertical; i++) {
        if (i != 0 && i % neural_network.output_horizontal === 0) {image_painter_output_squares_container.appendChild(document.createElement("br"));}

        var image_painter_square_template = document.getElementById("image_painter_square_template").content.cloneNode(true);
        image_painter_output_squares_container.appendChild(image_painter_square_template);
    }
}


function empty_training_images() {
    training_images = [];
    document.getElementById("training_images_container").innerHTML = "";
}


function initialize_weights_display() {
    var weights_display_input_squares_container = document.getElementById("weights_display_input_squares_container");
    var weights_display_output_squares_container = document.getElementById("weights_display_output_squares_container");

    weights_display_input_squares_container.innerHTML = "";
    weights_display_output_squares_container.innerHTML = "";

    for(var i = 0; i < neural_network.input_horizontal * neural_network.input_vertical; i++) {
        if (i != 0 && i % neural_network.input_horizontal === 0) {weights_display_input_squares_container.appendChild(document.createElement("br"));}
        var weights_display_square_template = document.getElementById("weights_display_square_template").content.cloneNode(true);
        weights_display_input_squares_container.appendChild(weights_display_square_template);
    }

    for(var i = 0; i < neural_network.output_horizontal * neural_network.output_vertical; i++) {
        if (i != 0 && i % neural_network.output_horizontal === 0) {weights_display_output_squares_container.appendChild(document.createElement("br"));}
        var weights_display_square_template = document.getElementById("weights_display_square_template").content.cloneNode(true);
        weights_display_output_squares_container.appendChild(weights_display_square_template);
    }
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

    // Set delete button's onclick to pass the index of the training image
    training_image_template.querySelector(".training_image_delete_button").setAttribute("onclick", `deleteTrainingImage(${current_training_images_number}, this)`);

    training_images_container.appendChild(training_image_template);
}




function deleteTrainingImage(index, button) {
    // Remove the training image from the array
    training_images.splice(index, 1);

    // Remove the HTML element
    const trainingImage = button.closest('.training_image');
    trainingImage.remove();

    // Update onclick attributes for all remaining delete buttons
    const remainingButtons = document.querySelectorAll('.training_image_delete_button');
    remainingButtons.forEach((btn, newIndex) => {
        btn.setAttribute('onclick', `deleteTrainingImage(${newIndex}, this)`);
    });
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


    updates_console_log_element.querySelector(".nn_console_item_div_content").innerHTML = message;

    if(error_or_not) {updates_console_log_element.querySelector(".nn_console_item_div").classList.add("nn_console_item_div_error");}

    updates_console_logs_container.appendChild(updates_console_log_element);

}



// Training neural network front-end logic

function train_neural_network() {
    if(!neural_network.initialized) {
        updates_console_update("Neural network not initialized", true);
        return 0;
    }

    var training_type = document.getElementById("training_mode_selector").getAttribute("data-value");
    var this_run_training_iterations = parseInt(document.getElementById("training_tab_train_iterations").getAttribute("data-value"));
    var training_data = [];

    if (training_type === "current_image") {
        var current_image_input = [];
        var current_image_output = [];

        document.getElementById("image_painter_input_squares_container").querySelectorAll(".image_painter_paint_square").forEach(element => {
            current_image_input.push((element.classList.contains("image_painter_paint_square_active") ? 1 : 0));
        });

        document.getElementById("image_painter_output_squares_container").querySelectorAll(".image_painter_paint_square").forEach(element => {
            current_image_output.push((element.classList.contains("image_painter_paint_square_active") ? 1 : 0));
        });

        training_data.push([current_image_input, current_image_output]);
    } else if (training_type === "stored_images") {
        if (training_images.length == 0) {
            updates_console_update("No stored training images", true);
            return 0;
        }
        training_data = training_images;
    }

    var training_object = {
        training_data: training_data,
        training_runs: this_run_training_iterations
    };

    send_training_images(training_object);
}

function send_training_images(training_object) {
    // Send AJAX request to nn_trainer.php
    fetch('/resources/logic/back_end/nn_back_end_logic/training/nn_trainer_2.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(training_object)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            updates_console_update("Neural network trained successfully on <b>" + training_object.training_runs + "</b> runs", false);
            updates_console_update("Average Loss: <b>" + data.average_loss + "</b>", false);
            updates_console_update("Median Loss: <b>" + data.median_loss + "</b>", false);
            update_weights_display(data.last_run_inputs, data.last_run_collapsed_outputs);
            document.getElementById("weights_display_average_loss").innerText = data.average_loss;
            document.getElementById("weights_display_median_loss").innerText = data.median_loss;
            neural_network.training_iterations += training_object.training_runs;
        } else {
            updates_console_update("Failed to train neural network: <b>" + data.message + "</b>", true);
        }
    })
    .catch(error => {
        updates_console_update("Error training neural network: <b>" + error + "</b>", true);
    });
}

function update_weights_display(inputs, outputs) {
    const inputContainer = document.getElementById("weights_display_input_squares_container");
    const outputContainer = document.getElementById("weights_display_output_squares_container");

    inputContainer.innerHTML = "";
    outputContainer.innerHTML = "";

    inputs.forEach((value, index) => {
        const square = document.createElement("div");
        square.classList.add("weights_display_paint_square");
        if (value === 1) {
            square.classList.add("weights_display_paint_square_active");
        }
        inputContainer.appendChild(square);
        if ((index + 1) % neural_network.input_horizontal === 0) {
            inputContainer.appendChild(document.createElement("br"));
        }
    });

    outputs.forEach((value, index) => {
        const square = document.createElement("div");
        square.classList.add("weights_display_paint_square");
        if (value === 1) {
            square.classList.add("weights_display_paint_square_active");
        }
        outputContainer.appendChild(square);
        if ((index + 1) % neural_network.output_horizontal === 0) {
            outputContainer.appendChild(document.createElement("br"));
        }
    });
}