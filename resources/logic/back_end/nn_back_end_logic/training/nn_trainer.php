<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/resources/logic/back_end/nn_back_end_logic/initialization/nn_data_structure.php');

// Subfunctions
function get_post_data() {
    return json_decode(file_get_contents('php://input'), true);
}

function check_training_data($training_object) {
    if ($training_object === null) return 'Invalid JSON data received';
    
    $required = ['training_data', 'training_runs'];
    foreach ($required as $field) {
        if (!isset($training_object[$field])) return "Missing field: {$field}";
    }
    return true;
}

function send_response($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
}

function forward_propagation($nn, $data, &$layer_inputs) {
    $inputs = $data['inputs'];
    $outputs = [];

    // Process through each layer
    $layer_input = $inputs;
    $layer_inputs = []; // Initialize layer_inputs array
    foreach ($nn->weights as $layer_weights) {
        $layer_inputs[] = $layer_input; // Store the input for the current layer
        $layer_output = [];
        foreach ($layer_weights as $neuron_weights) {
            $neuron_output = 0;
            foreach ($neuron_weights as $index => $weight) {
                $neuron_output += $weight * $layer_input[$index];
            }
            // Apply activation function (e.g., sigmoid)
            $layer_output[] = 1 / (1 + exp(-$neuron_output));
        }
        $layer_input = $layer_output;
    }
    $outputs = $layer_input;

    return $outputs;
}

function calculate_loss($nn, $data, $predicted_output) {
    $actual_output = $data['outputs'];
    $loss = 0;
    for ($i = 0; $i < count($actual_output); $i++) {
        $loss += pow($predicted_output[$i] - $actual_output[$i], 2);
    }
    return $loss / count($actual_output);
}

function backpropagation($nn, $data, $predicted_output, $learning_rate, $layer_inputs) {
    $actual_output = $data['outputs'];

    // Calculate output layer error
    $output_errors = [];
    for ($i = 0; $i < count($actual_output); $i++) {
        $output_errors[$i] = $predicted_output[$i] - $actual_output[$i];
    }

    // Backpropagate the error
    $errors = $output_errors;
    for ($layer = count($nn->weights) - 1; $layer >= 0; $layer--) {
        $layer_errors = [];
        $layer_weights = $nn->weights[$layer];
        for ($neuron = 0; $neuron < count($layer_weights); $neuron++) {
            $neuron_error = 0;
            foreach ($nn->weights[$layer][$neuron] as $weight_index => $weight) {
                if (isset($errors[$neuron])) {
                    $neuron_error += $errors[$neuron] * $weight;
                    // Update weight
                    if (isset($layer_inputs[$layer][$weight_index])) {
                        $nn->weights[$layer][$neuron][$weight_index] -= $learning_rate * $errors[$neuron] * $layer_inputs[$layer][$weight_index];
                    }
                }
            }
            $layer_errors[$neuron] = $neuron_error;
        }
        $errors = $layer_errors;
    }
}

function calculate_median($values) {
    sort($values);
    $count = count($values);
    $middle = floor(($count - 1) / 2);
    if ($count % 2) {
        return $values[$middle];
    } else {
        return ($values[$middle] + $values[$middle + 1]) / 2.0;
    }
}

function adapt_learning_rate($initial_rate, $current_run, $total_runs) {
    // Example: Decrease learning rate linearly over time
    return $initial_rate * (1 - ($current_run / $total_runs));
}

function collapse_output($output) {
    return array_map(function($value) {
        return $value >= 0.5 ? 1 : 0;
    }, $output);
}

function perform_training($nn, $training_data, $training_runs, &$average_loss, &$median_loss, &$last_run_inputs, &$last_run_non_collapsed_outputs, &$last_run_collapsed_outputs) {
    $data_count = count($training_data);
    $losses = [];
    $initial_learning_rate = 0.01; // Initial learning rate

    for ($run = 0; $run < $training_runs; $run++) {
        // Pick a random sample from the training data
        $random_index = array_rand($training_data);
        $data = ["inputs" => $training_data[$random_index][0], "outputs" => $training_data[$random_index][1]];

        // Adapt learning rate
        $learning_rate = adapt_learning_rate($initial_learning_rate, $run, $training_runs);

        $layer_inputs = []; // Initialize layer_inputs array for this run
        $predicted_output = forward_propagation($nn, $data, $layer_inputs);
        $loss = calculate_loss($nn, $data, $predicted_output);
        $losses[] = $loss;
        backpropagation($nn, $data, $predicted_output, $learning_rate, $layer_inputs);

        // Store outputs for the last run
        if ($run == $training_runs - 1) {
            $last_run_inputs = $data['inputs'];
            $last_run_non_collapsed_outputs = $predicted_output;
            $last_run_collapsed_outputs = collapse_output($predicted_output);
        }
    }
    $nn->store();

    // Calculate average and median loss for the last run
    $last_run_losses = array_slice($losses, -1);
    $average_loss = array_sum($last_run_losses) / count($last_run_losses);
    $median_loss = calculate_median($last_run_losses);
}

// Main function
function nn_trainer_main() {
    session_start();
    if (!isset($_SESSION['neural_network'])) {
        send_response(['success' => false, 'message' => 'No neural network found in session']);
        exit;
    }

    $nn = $_SESSION['neural_network'];
    $training_object = get_post_data();
    $check = check_training_data($training_object);

    if ($check === true) {
        $training_data = $training_object['training_data'];
        $training_runs = $training_object['training_runs'];

        $average_loss = 0;
        $median_loss = 0;
        $last_run_inputs = [];
        $last_run_non_collapsed_outputs = [];
        $last_run_collapsed_outputs = [];
        perform_training($nn, $training_data, $training_runs, $average_loss, $median_loss, $last_run_inputs, $last_run_non_collapsed_outputs, $last_run_collapsed_outputs);

        $response = [
            'success' => true,
            'message' => 'Training completed successfully',
            'average_loss' => $average_loss,
            'median_loss' => $median_loss,
            'last_run_inputs' => $last_run_inputs,
            'last_run_non_collapsed_outputs' => $last_run_non_collapsed_outputs,
            'last_run_collapsed_outputs' => $last_run_collapsed_outputs
        ];
    } else {
        $response = ['success' => false, 'message' => $check];
    }

    send_response($response);
}

// Chain of events
nn_trainer_main();
?>
