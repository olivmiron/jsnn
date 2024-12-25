<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/resources/logic/back_end/nn_back_end_logic/initialization/nn_data_structure.php');
// Subfunctions
function get_data() {return json_decode(file_get_contents('php://input'), true);}

function check_data($neural_network) {
    if ($neural_network === null) return 'Invalid JSON data received';
    
    $required = ['name', "initialization_type", "training_iterations", 'input_vertical', 'input_horizontal', 'output_vertical', 'output_horizontal', 'deep_layers', 'neurons_per_deep_layer'];
    
    foreach ($required as $field) {
        if (!isset($neural_network[$field])) return "Missing field: {$field}";
    }
    return true;
}

function send_response($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Neural Network Object Functions
function create_nn_object($data) {
    return new NeuralNetworkStructure($data);
}

// Main function
function nn_initializer_main() {
    $data = get_data();
    $check = check_data($data);
    
    if ($check === true) {
        $nn = create_nn_object($data);
        $nn->store();
        $response = [
            'success' => true,
            'message' => 'Neural network initialized and stored successfully', 
            // "nn_object" => json_encode($nn)
        ];
    } else {
        $response = [
            'success' => false,
            'message' => $check
        ];
    }

    send_response($response);
}

// Chain of events
nn_initializer_main();
?>