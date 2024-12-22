<?php
class NeuralNetworkStructure {
    public $name;
    public $initialization_type;
    public $input_dimensions;
    public $output_dimensions;
    public $deep_layers;
    public $neurons_per_layer;
    public $weights;

    public function __construct($data) {
        $this->name = $data['name'];
        $this->initialization_type = $data['initialization_type'];
        $this->input_dimensions = [
            'vertical' => $data['input_vertical'],
            'horizontal' => $data['input_horizontal']
        ];
        $this->output_dimensions = [
            'vertical' => $data['output_vertical'],
            'horizontal' => $data['output_horizontal']
        ];
        $this->deep_layers = $data['deep_layers'];
        $this->neurons_per_layer = $data['neurons_per_deep_layer'];
        $this->initialize_weights();
    }

    private function initialize_weights() {
        $this->weights = [];
        $prev_layer_size = $this->input_dimensions['vertical'] * $this->input_dimensions['horizontal'];
        
        // Initialize weights for each layer
        for ($i = 0; $i < $this->deep_layers; $i++) {
            $this->weights[$i] = $this->get_weights_by_type($prev_layer_size, $this->neurons_per_layer);
            $prev_layer_size = $this->neurons_per_layer;
        }
        
        // Output layer weights
        $output_size = $this->output_dimensions['vertical'] * $this->output_dimensions['horizontal'];
        $this->weights[$this->deep_layers] = $this->get_weights_by_type($prev_layer_size, $output_size);
    }

    private function get_weights_by_type($inputs, $outputs) {
        switch ($this->initialization_type) {
            case 'xavier':
                return $this->initialize_weights_xavier($inputs, $outputs);
            case 'he':
                return $this->initialize_weights_he($inputs, $outputs);
            case 'random_normal':
                return $this->initialize_weights_random_normal($inputs, $outputs);
            case 'random_uniform':
                return $this->initialize_weights_random_uniform($inputs, $outputs);
            case 'zeros':
                return $this->initialize_weights_zeros($inputs, $outputs);
            case 'ones':
                return $this->initialize_weights_ones($inputs, $outputs);
        }
    }

    private function initialize_weights_xavier($inputs, $outputs) {
        $weights = [];
        $std_dev = sqrt(2.0 / ($inputs + $outputs));
        for ($i = 0; $i < $inputs; $i++) {
            for ($j = 0; $j < $outputs; $j++) {
                $weights[$i][$j] = (rand(-1000, 1000) / 1000) * $std_dev;
            }
        }
        return $weights;
    }

    private function initialize_weights_he($inputs, $outputs) {
        $weights = [];
        $std_dev = sqrt(2.0 / $inputs);
        for ($i = 0; $i < $inputs; $i++) {
            for ($j = 0; $j < $outputs; $j++) {
                $weights[$i][$j] = (rand(-1000, 1000) / 1000) * $std_dev;
            }
        }
        return $weights;
    }

    private function initialize_weights_random_normal($inputs, $outputs) {
        $weights = [];
        for ($i = 0; $i < $inputs; $i++) {
            for ($j = 0; $j < $outputs; $j++) {
                $weights[$i][$j] = (rand(-1000, 1000) / 1000) * 0.1;
            }
        }
        return $weights;
    }

    private function initialize_weights_random_uniform($inputs, $outputs) {
        $weights = [];
        for ($i = 0; $i < $inputs; $i++) {
            for ($j = 0; $j < $outputs; $j++) {
                $weights[$i][$j] = rand(-1000, 1000) / 1000;
            }
        }
        return $weights;
    }

    private function initialize_weights_zeros($inputs, $outputs) {
        $weights = [];
        for ($i = 0; $i < $inputs; $i++) {
            for ($j = 0; $j < $outputs; $j++) {
                $weights[$i][$j] = 0.0;
            }
        }
        return $weights;
    }

    private function initialize_weights_ones($inputs, $outputs) {
        $weights = [];
        for ($i = 0; $i < $inputs; $i++) {
            for ($j = 0; $j < $outputs; $j++) {
                $weights[$i][$j] = 1.0;
            }
        }
        return $weights;
    }

}