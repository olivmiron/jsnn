<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/resources/logic/back_end/nn_back_end_logic/initialization/nn_data_structure.php');

// Subfunctions
function get_post_data() {
    return json_decode(file_get_contents('php://input'), true);
}

function send_response($response) {
    header('Content-Type: application/json');
    echo json_encode($response);

    if(!$response['success']) {exit;}
}

// global training parameters
$nn; $training_state = []; $training_params;
$response_object = [
    'input' => [],
    'output' => [],
    'average_loss' => '',
    'median_loss' => ''
];


function nn_init() {
    global $nn;
    
    session_start();
    if (!isset($_SESSION['neural_network'])) {send_response(['success' => false, 'message' => 'No neural network found in session']);}
    $nn = $_SESSION['neural_network'];
}


function training_params_init() {
    global $training_params;
    $training_params = get_post_data();

    $training_params_init_state[0] = true;
    $training_params_init_state[1] = '';

    if ($training_params === null) {$training_params_init_state[0] = false;$training_params_init_state[1] = 'Invalid JSON data received';}
    
    $required = ['training_data', 'training_runs'];
    foreach ($required as $field) {
        if (!isset($training_params[$field])) {
            $training_params_init_state[0] = false;$training_params_init_state[1] = "Missing field: {$field}";
        }
        
    }
    
    if(!$training_params_init_state[0]) {send_response(['success' => false, 'message' => $training_params_init_state[1]]);}
    
    $training_params["current_training_run"] = 0;
    $training_params["learning_rate"] = 0.01;
    $training_params["derivative"] = "sigmoid"; // Default activation function is sigmoid; CAN BE CHANGED
}

function training_state_init() {
    global $nn, $training_state;
    $training_state = [
        'nn_structure' => [
            'input_dimensions' => $nn->input_dimensions,
            'deep_layers' => $nn->deep_layers,
            'neurons_per_layer' => $nn->neurons_per_layer,
            'output_dimensions' => $nn->output_dimensions
        ],
        'training_data' => [
            'weights' => $nn->weights,
            'neurons' => [],
            'losses' => []
        ]
    ];

    // Initialize neurons array
    $input_size = $nn->input_dimensions['vertical'] * $nn->input_dimensions['horizontal'];
    $training_state['training_data']['neurons'][0] = array_fill(0, $input_size, 0);

    for ($i = 1; $i <= $nn->deep_layers; $i++) {
        $training_state['training_data']['neurons'][$i] = array_fill(0, $nn->neurons_per_layer, 0);
    }

    $output_size = $nn->output_dimensions['vertical'] * $nn->output_dimensions['horizontal'];
    $training_state['training_data']['neurons'][$nn->deep_layers + 1] = array_fill(0, $output_size, 0);


    // Initialize errors array
    foreach ($training_state['training_data']['neurons'] as $layer_neurons) {
        $training_state['training_data']['losses'][] = array_fill(0, count($layer_neurons), 0);
    }
}

function main() {
    global $nn, $training_state, $training_params, $response_object;

    // I. $nn
    nn_init();

    // II. $training_params
    training_params_init();

    // III. Training state initialization
    training_state_init();

    // IV. Training
    train();
    
    // replace the weights in $nn with the corrected weights in $training_state
    // save the updated $nn in the session and in the .json file
    $nn->weights = $training_state['training_data']['weights'];
    $_SESSION['neural_network'] = $nn;
    $nn->store();

    // V. Response
    // ...
    $response_object['success'] = true;
    send_response($response_object);
}

function train() {
    global $training_state, $training_params, $response_object;

    for ($training_params['current_training_run'] = 0; $training_params['current_training_run'] < $training_params['training_runs']; $training_params['current_training_run']++) {
        // Pick a random sample from the training data
        $random_training_index = array_rand($training_params['training_data']);
        $training_data_io = [
            "inputs" => $training_params['training_data'][$random_training_index][0], 
            "outputs" => $training_params['training_data'][$random_training_index][1]
        ];

        // Adapt learning rate
        adapt_learning_rate();

        train_forward($training_data_io);
        
        calculate_output_losses($training_data_io);

        // get_error_and_stop();
        
        train_backward($training_data_io);

        // Update weights after backpropagation
        train_weights_update();

        // Store outputs for the last run
        if ($training_params['current_training_run'] == $training_params['training_runs'] - 1) {
            $response_object['last_run_inputs'] = $training_data_io['inputs'];
            $response_object['last_run_outputs'] = $training_state['training_data']['neurons'][count($training_state['training_data']['neurons']) - 1];
            $response_object['last_run_collapsed_outputs'] = collapse_output($training_state['training_data']['neurons'][count($training_state['training_data']['neurons']) - 1]);
            $response_object['average_loss'] = calculate_average_loss();
            $response_object['median_loss'] = calculate_median_loss();
        }
    }
}

function collapse_output($output) {
    $collapsed_output = [];
    foreach ($output as $neuron_output) {
        $collapsed_output[] = round($neuron_output);
    }
    return $collapsed_output;
}

function adapt_learning_rate() {
    global $training_params;
    $training_params['learning_rate'] = $training_params['learning_rate'] / ($training_params['current_training_run'] + 1);
}

function train_forward($training_data_io) {
    global $training_state;

    // Set input layer neurons
    $training_state['training_data']['neurons'][0] = $training_data_io['inputs'];

    // Forward propagation through each layer
    for ($layer = 1; $layer <= $training_state["nn_structure"]["deep_layers"] + 1; $layer++) {
        $previous_layer_neurons = $training_state['training_data']['neurons'][$layer - 1];
        $current_layer_neurons = [];

        // Iterate through each neuron in the current layer
        for ($neuron_index = 0; $neuron_index < count($training_state["training_data"]["weights"][$layer - 1][0]); $neuron_index++) {
            $neuron_input = 0;

            // Sum the weighted inputs from the previous layer neurons
            for ($prev_neuron_index = 0; $prev_neuron_index < count($previous_layer_neurons); $prev_neuron_index++) {
                $neuron_input += $training_state["training_data"]["weights"][$layer - 1][$prev_neuron_index][$neuron_index] * $previous_layer_neurons[$prev_neuron_index];
            }

            // Apply activation function
            $current_layer_neurons[] = activation_function($neuron_input);
        }

        $training_state['training_data']['neurons'][$layer] = $current_layer_neurons;
    }
}

function activation_function($input) {
    global $training_params;
    switch ($training_params["derivative"]) {
        case 'relu':
            return max(0, $input);
        case 'tanh':
            return tanh($input);
        case 'sigmoid':
        default:
            return 1 / (1 + exp(-$input));
    }
}


function calculate_output_losses($training_data_io) {
    global $training_state;

    $actual_output = $training_data_io['outputs'];
    $predicted_output = $training_state['training_data']['neurons'][count($training_state['training_data']['neurons']) - 1];
    $losses = [];

    for ($i = 0; $i < count($actual_output); $i++) {
        $losses[] = pow($predicted_output[$i] - $actual_output[$i], 2);
    }

    // Store the losses for the last layer of neurons
    $training_state['training_data']['losses'][count($training_state['training_data']['neurons']) - 1] = $losses;
}


function train_backward($training_data_io) {
    global $training_state, $training_params;

    // Backpropagate errors through hidden layers
    for ($layer = count($training_state['training_data']['neurons']) - 2; $layer >= 0; $layer--) {
        $current_layer_errors = [];
        $next_layer_errors = $training_state['training_data']['losses'][$layer + 1];
        $current_layer_neurons = $training_state['training_data']['neurons'][$layer];
        $next_layer_weights = $training_state['training_data']['weights'][$layer];

        for ($i = 0; $i < count($current_layer_neurons); $i++) {
            $error = 0;
            for ($j = 0; $j < count($next_layer_errors); $j++) {
                $error += $next_layer_errors[$j] * $next_layer_weights[$i][$j];
            }
            $current_layer_errors[] = $error * $current_layer_neurons[$i] * (1 - $current_layer_neurons[$i]); // Derivative of sigmoid
        }
        $training_state['training_data']['losses'][$layer] = $current_layer_errors;
    }
}

function train_weights_update() {
    global $training_state, $training_params;

    // Update weights
    for ($layer = 0; $layer < count($training_state['training_data']['weights']); $layer++) {
        $current_layer_neurons = $training_state['training_data']['neurons'][$layer];
        $next_layer_errors = $training_state['training_data']['losses'][$layer + 1];
        $learning_rate = $training_params['learning_rate'];

        for ($i = 0; $i < count($next_layer_errors); $i++) {
            for ($j = 0; $j < count($current_layer_neurons); $j++) {
                $training_state['training_data']['weights'][$layer][$j][$i] -= $learning_rate * $next_layer_errors[$i] * $current_layer_neurons[$j];
            }
        }
    }
}

function calculate_average_loss() {
    global $training_state;
    $losses = $training_state['training_data']['losses'];
    $last_layer_losses = end($losses);
    $total_loss = array_sum($last_layer_losses);
    $average_loss = $total_loss / count($last_layer_losses);
    return $average_loss;
}

function calculate_median_loss() {
    global $training_state;
    $losses = $training_state['training_data']['losses'];
    $last_layer_losses = end($losses);
    sort($last_layer_losses);
    $count = count($last_layer_losses);
    $middle = floor($count / 2);

    if ($count % 2) {
        $median_loss = $last_layer_losses[$middle];
    } else {
        $median_loss = ($last_layer_losses[$middle - 1] + $last_layer_losses[$middle]) / 2;
    }

    return $median_loss;
}



function get_error_and_stop() {
    global $training_state, $training_params;
    echo json_encode([
        "success" => false,
        'training_state' => $training_state,
        'training_params' => $training_params,

    ]);
    die;
}

// Chain of events
main();
?>
