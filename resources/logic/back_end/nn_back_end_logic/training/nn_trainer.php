<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/resources/logic/back_end/nn_back_end_logic/initialization/nn_data_structure.php');

session_start();
if (!isset($_SESSION['neural_network'])) {
    echo json_encode(['success' => false, 'message' => 'No neural network found in session']);
    exit;
}

$nn = $_SESSION['neural_network'];

// Get the POST data
$input = file_get_contents('php://input');
$training_object = json_decode($input, true);

if ($training_object === null) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

// Process the training object
$training_data = $training_object['training_data'];
$training_runs = $training_object['training_runs'];

// Add your training logic here


// 
// 
// 
// 
// 
// 
//


// Re-store the nn object into .json
$nn->store();

// Return success response
echo json_encode(['success' => true, 'message' => 'Training completed successfully']);
?>
