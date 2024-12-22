<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/resources/logic/back_end/nn_back_end_logic/initialization/nn_data_structure.php');
// Subfunctions
function get_data() {return json_decode(file_get_contents('php://input'), true);}

function check_data($neural_network) {
    if ($neural_network === null) return 'Invalid JSON data received';
    
    $required = ['name', "initialization_type", 'input_vertical', 'input_horizontal', 'output_vertical', 'output_horizontal', 'deep_layers', 'neurons_per_deep_layer'];
    
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

function store_nn($nn_object) {
    $_SESSION['neural_network'] = $nn_object;

    $json_data = json_encode($nn_object);
    $date = date('Y-m-d_His');
    $safe_name = empty($nn_object->name) ? 'nn_' . $date : $nn_object->name . '_' . $date;
    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/resources/specific/neural_network_weights/' . $safe_name . '.json';
    file_put_contents($file_path, $json_data);
    
    return true;
}

// Main function
function nn_initializer_main() {
    $data = get_data();
    $check = check_data($data);
    
    if ($check === true) {
        session_start();
        $nn = create_nn_object($data);
        store_nn($nn);
        $response = [
            'success' => true,
            'message' => 'Neural network initialized and stored successfully', 
            "nn_object" => json_encode($nn)
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