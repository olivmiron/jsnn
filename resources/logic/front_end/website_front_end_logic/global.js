//// custom UI elements handling


function dropdown_toggle(dropdown) {event.stopPropagation();
    if(dropdown.getAttribute("data-opened") == "0") {

        dropdown.querySelector(".dropdown_list").style.top = "";dropdown.querySelector(".dropdown_list").style.bottom = "";
        dropdown.querySelector(".dropdown_list").style.display = "block";


        var space_down_below = window.innerHeight - (dropdown.querySelector(".dropdown_list").getBoundingClientRect().y + dropdown.querySelector(".dropdown_list").offsetHeight);
        if(space_down_below < 2) {dropdown.querySelector(".dropdown_list").style.top = "unset";dropdown.querySelector(".dropdown_list").style.bottom = "var(--dropdown_list_distance_from_dropdown)";}


        dropdown.querySelector(".dropdown_main_arrow").style.rotate = "180deg";
        dropdown.setAttribute("data-opened", "1");
    }
    else {
        dropdown.querySelector(".dropdown_list").style.display = "none";
        dropdown.querySelector(".dropdown_main_arrow").style.rotate = "0deg";
        dropdown.setAttribute("data-opened", "0");
    }
}


function dropdown_select(dropdown_element) {
    dropdown_element.closest(".dropdown").setAttribute("data-value", dropdown_element.getAttribute("data-value"));
    dropdown_element.closest(".dropdown").querySelector(".dropdown_main_text").innerText = dropdown_element.innerText;

    dropdown_element.parentElement.querySelectorAll(".dropdown_list_element").forEach(element => {
        element.classList.remove("dropdown_list_active_element");
    });
    dropdown_element.classList.add("dropdown_list_active_element");

    dropdown_toggle(dropdown_element.closest(".dropdown"));
}

// the drop down




// the checkbox





//// global functionality (error handling, ajax functions)
