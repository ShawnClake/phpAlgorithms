<?php namespace Neuralnetworks;

class NeuralNetwork
{
    private $outputNeurons = 1;
    private $inputNeurons = 2;
    private $hiddenNeurons = 4;

    private $hiddenLayers = 3;

    private $setCount = 21; // How many training sets we have

    private $inputs = []; // The inputs we are training with

    private $targets = []; // What we are training with; the expected result

    private $weights = []; // Weights array. Each outer array is for a layer. [Layer][HigherLevelNode][LowerLevelNode] = WeightValue
    private $hiddens = []; // What the narual network calculates at each hidden neuron [Layer][WhichNode] = ValueAtNode
    private $outputs = []; // What the neural network calculates as an output

    private $iterations = 10000;
    private $learningRate = 0.7;

    public function __construct($inputs, $targets, $iterations)
    {
        $this->inputs = $inputs;
        $this->targets = $targets;
        $this->iterations = $iterations;

        $this->initWeights();
        $this->train($this->targets);
    }

    private function sigmoid($x, $derivative = false)
    {
        if ($derivative)
            return $x * (1 - $x);
        return 1 / (1 + exp(-$x));
    }

    public function forwardPropagation($inputSet)
    {
        $this->hiddens = [];
        $this->outputs = [];

        // Adds bias neuron to input layer
        $inputSet[] = 1;

        // Takes input neurons and propagates it to the first layer of hidden neurons
        for($i = 0; $i < $this->hiddenNeurons; $i++)
        {
            $sum = 0;
            for($j = 0; $j < $this->inputNeurons + 1; $j++)
                $sum += $inputSet[$j] * $this->weights[0][$j][$i];

            $this->hiddens[0][$i] = $this->sigmoid($sum);
        }

        // Adds bias neuron to each hidden layer
        //for($i = 0; $i < $hiddenLayers; $i++)
        $this->hiddens[0][] = 1;

        // Propagates each hidden layer to the next
        for($i = 0; $i < $this->hiddenLayers - 1; $i++)    // Loops through and calculates the weights BETWEEN hidden layers. Thus one less weight then there are hidden layers
        {
            for($j = 0; $j < $this->hiddenNeurons; $j++)   // Loops through the lower hidden layer of neurons
            {
                $sum = 0;
                for($k = 0; $k < $this->hiddenNeurons + 1; $k++)    // Loops through the higher hidden layer of neurons. Also adds 1 to loop counter because of BIAS node
                    $sum += $this->hiddens[$i][$k] * $this->weights[$i + 1][$k][$j];   // Creates a sum of HighNeuron * WeightBetweenHighNeuronAndThisNeuron

                $this->hiddens[$i + 1][$j] = $this->sigmoid($sum);
            }

            $this->hiddens[$i + 1][] = 1;

        }

        // Takes last hidden layer and propogates to the output layer
        for($i = 0; $i < $this->outputNeurons; $i++)
        {
            $sum = 0;
            for($j = 0; $j < $this->hiddenNeurons + 1; $j++)
                $sum += $this->hiddens[$this->hiddenLayers - 1][$j] * $this->weights[$this->hiddenLayers][$j][$i];

            $this->outputs[$i] = $this->sigmoid($sum);
        }
    }

    private function trainNetwork($inputSet, $targets, $print_error = false)
    {
        $delta_hiddens = [];
        $delta_outputs = [];

        $inputSet[] = 1; // Is this needed since its already done during forward propagation?

        for($i = 0; $i < $this->hiddenLayers; $i++)
            $this->hiddens[$i][] = 1;

        // FORMULATING THE NESECARY WEIGHT CHANGES GO BELOW

        for($i = 0; $i < $this->outputNeurons; $i++)
        {
            $error = $targets[$i] - $this->outputs[$i];
            $delta_outputs[$i] = $this->sigmoid($this->outputs[$i], true) * $error;
        }

        $avg_sum = 0;

        for($i = 0; $i < $this->hiddenNeurons + 1; $i++)
        {
            $error = 0;
            for($j = 0; $j < $this->outputNeurons; $j++)
                $error += $this->weights[$this->hiddenLayers][$i][$j] * $delta_outputs[$j];

            $delta_hiddens[0][$i] = $this->sigmoid($this->hiddens[$this->hiddenLayers - 1][$i], true) * $error;

            if($print_error)
                $avg_sum += $error;
        }

        // FORMULATING THE NESECARY WEIGHT CHANGES FOR HIDDEN LAYER TO HIDDEN LAYER
        for($i = 0; $i < $this->hiddenLayers - 1; $i++)
        {
            for($j = 0; $j < $this->hiddenNeurons + 1; $j++)
            {
                $error = 0;
                for($k = 0; $k < $this->hiddenNeurons; $k++)
                    $error += $this->weights[$this->hiddenLayers - 1 - $i][$j][$k] * $delta_hiddens[$i][$k];

                $delta_hiddens[$i + 1][$j] = $this->sigmoid($this->hiddens[$this->hiddenLayers - 2 - $i][$j], true) * $error;

            }
        }

        // DEBUG GO BELOW
        if($print_error)
            echo 'Error: ' . $avg_sum / ($this->hiddenNeurons + 1) . '<br><br>';


        // APPLYING THE WEIGHT CHANGES GO BELOW
        for($i = 0; $i < $this->outputNeurons; $i++)
        {
            for($j = 0; $j < $this->hiddenNeurons + 1; $j++)
                $this->weights[$this->hiddenLayers][$j][$i] += $this->learningRate * $delta_outputs[$i] * $this->hiddens[$this->hiddenLayers - 1][$j];

        }

        for($i = 0; $i < $this->hiddenLayers - 1; $i++)
        {
            for($j = 0; $j < $this->hiddenNeurons; $j++)
            {
                for($k = 0; $k < $this->hiddenNeurons + 1; $k++)
                    $this->weights[$this->hiddenLayers - 1 - $i][$k][$j] += $this->learningRate * $delta_hiddens[$i][$j] * $this->hiddens[$this->hiddenLayers - 2 - $i][$k];

            }


        }

        for($i = 0; $i < $this->hiddenNeurons; $i++)
        {
            for($j = 0; $j < $this->inputNeurons + 1; $j++)
            {
                //echo 'input: ' . $inputs[$j] . '<br>';
                $this->weights[0][$j][$i] += $this->learningRate * $delta_hiddens[$this->hiddenLayers - 1][$i] * $inputSet[$j];
            }

        }

    }

    public function train($targets)
    {
        for($i = 0; $i < $this->iterations; $i++)
        {
            if($i % 1000 == 0)
                //echo "Iteration: " . $i . '<br>';

            for($j = 0; $j < $this->setCount; $j++)
            {
                $input_set = $this->inputs[$j];
                $target_set = $targets[$j];

                $this->forwardPropagation($input_set);

                if($i % 1000 == 0)
                {
                    $print_error = false;
                    //echo 'Printing errors for iteration ' . $i . '<br>';
                }
                else
                    $print_error = false;

                $this->trainNetwork($input_set, $target_set, $print_error);

            }

        }

    }

    private function initWeights()
    {
        $this->weights = [];

        for($i = 0; $i < $this->hiddenNeurons; $i++)
        {
            for($j = 0; $j < $this->inputNeurons + 1; $j++)
            {
                $randomWeight = 2 * lcg_value() - 1;
                $this->weights[0][$j][$i] = $randomWeight;
            }
        }

        for($i = 0; $i < $this->hiddenLayers - 1; $i++)
        {
            for($j = 0; $j < $this->hiddenNeurons; $j++)
            {
                for($k = 0; $k < $this->hiddenNeurons + 1; $k++)
                {
                    $randomWeight = 2 * lcg_value() - 1;
                    $this->weights[$i + 1][$k][$j] = $randomWeight;
                }
            }
        }

        for($i = 0; $i < $this->outputNeurons; $i++)
        {
            for($j = 0; $j < $this->hiddenNeurons + 1; $j++)
            {
                $randomWeight = 2 * lcg_value() - 1;
                $this->weights[$this->hiddenLayers][$j][$i] = $randomWeight;
            }
        }
    }

    public function getOutputs()
    {
        return $this->outputs;
    }

    public function printOutputs()
    {
        echo json_encode($this->getOutputs()) . '<br><br>';
    }

    public function solve($inputs, $iterations = 1)
    {
        $sum = [];
        $outsize = 0;
        for($i = 0; $i < $iterations; $i++)
        {
            if($i != 0)
            {
                $this->weights = [];
                $this->hiddens = [];
                $this->outputs = [];
                $this->initWeights();
                $this->train($this->targets);
            }

            $this->forwardPropagation($inputs);
            $output = $this->getOutputs();
            if($i == 0)
                $outsize = count($output);
            for($j = 0; $j < $outsize; $j++)
            {
                if(!isset($sum[$j]))
                    $sum[$j] = 0;
                $sum[$j] += $output[$j];
            }
        }
        echo '<br>Result: <br>';
        for($j = 0; $j < $outsize; $j++)
            echo '<i>Output ' . ($j + 1) . ':</i> ' . ($sum[$j] / $iterations) . '<br>';
    }
}

$i = [
    [0.25,0.14925373],
    [0.125,0.09090909],
    [0.3333,0.11111111],
    [0.5,0.16666667],
    [0.2,0.18181818],
    [0.3333,0.13333333],
    [0.3333,0.14492754],
    [0.3333,0.11363636],
    [0.5,0.20000000],
    [0.5,0.21052632],
    [0.5,0.06666667],
    [0.25,0.10000000],
    [0.25,0.11792453],
    [0.2,0.09090909],
    [0.166666667,0.05000000],
    [0.166666667,0.08000000],
    [0.142857143,0.01998002],
    [0.1,0.04016064],
    [0.1,0.03984064],
    [0.1,0.03584064],
    [0.1,0.03084064],
];

$t = [
    [0],
    [0],
    [1],
    [1],
    [0],
    [1],
    [0],
    [1],
    [1],
    [0],
    [1],
    [1],
    [0],
    [0],
    [1],
    [0],
    [1],
    [0],
    [1],
    [1],
    [1],
];

$iteration = 10000;

echo '<ul>';

for($loop = 0; $loop < 20; $loop++)
{
    $nn = new NeuralNetwork($i, $t, $iteration);

    echo '<li>Iterations: ' . $iteration . '<br>';

    $nn->solve(
        [0.1, 0.028160643], 100
    );

    echo '</li><hr>';

    $nn = null;

    $iteration += 10000;
}

echo '</ul>';


//$nn->forwardPropagation([0.1, 0.000000064]);
//$nn->printOutputs();
