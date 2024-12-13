

function index_change_widgets_per_row(widgets_per_row) {
    document.querySelector(':root').style.setProperty("--widgets_per_row", widgets_per_row);
    console.log(widgets_per_row);
}