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