<?php namespace Neuralnetworks;

/**
 * Neural Network by Shawn Clake
 * Class NeuralNetwork
 * Neural Network is licensed under the MIT license.
 *
 * @author Shawn Clake <shawn.clake@gmail.com>
 * @link https://github.com/ShawnClake/phpAlgorithms
 * @link http://shawnclake.com/
 * @link http://shawnclake.com/blog
 *
 * @license https://github.com/ShawnClake/phpAlgorithms/blob/master/LICENSE MIT
 *
 * @package Neuralnetworks
 */
class NeuralNetwork
{
    private $outputNeurons = 4; // How many neurons are on the output layer
    private $inputNeurons = 13; // How many neurons are on the input layer
    private $hiddenNeurons = 5; // How many neurons are on each hidden layer. Alternative formula to calculate:

    private $hiddenLayers = 1; // How many hidden layers there are.

    private $setCount = 32; // How many training sets we have

    private $inputs = []; // The inputs we are training with

    private $targets = []; // What we are training with; the expected result

    private $weights = []; // Weights array. Each outer array is for a layer. [Layer][HigherLevelNode][LowerLevelNode] = WeightValue
    private $hiddens = []; // What the neural network calculates at each hidden neuron [Layer][WhichNode] = ValueAtNode
    private $outputs = []; // What the neural network calculates as an output

    private $iterations = 10000; // How many iterations we run over the training set to train the neural network
    private $learningRate = 0.7; // A modifier to reduce drastic (and sometimes incorrect) changes to weights

    public $debugCounter = 0;

    /**
     * NeuralNetwork constructor.
     * @param $inputs
     * @param $targets
     * @param $iterations
     * @param array $weights
     */
    public function __construct($inputs, $targets, $iterations, $weights = [])
    {
        $this->inputs = $inputs;
        $this->targets = $targets;
        $this->iterations = $iterations;

        $this->weights = $weights;

        if(empty($this->weights))
        {
            $this->initWeights();
            $this->train($this->targets);
        }
    }

    /**
     * A sigmoid function calculator
     * @param $x
     * @param bool $derivative boolean when this is true, take the derivative of sigmoid
     * @return float|int
     */
    private function sigmoid($x, $derivative = false)
    {
        if ($derivative)
            return $x * (1 - $x);
        return 1 / (1 + exp(-$x));
    }

    /**
     * Run a given input set through the neural network and calculate an answer based on current weights
     * @param $inputSet
     */
    public function forwardPropagation($inputSet)
    {
        $this->hiddens = []; // Reset Hiddens and Outputs array otherwise we get a mem leak from appending bias nodes at the end
        $this->outputs = [];

        $this->debugCounter++;

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

    /**
     * Run backwards through the neural network with the current weights. Alter the weights by going from the expected target back to the input set and calculate errors along the way
     * @param $inputSet
     * @param $targets
     * @param bool $print_error
     */
    private function trainNetwork($inputSet, $targets, $print_error = false)
    {
        $delta_hiddens = [];
        $delta_outputs = [];

        $inputSet[] = 1; // Is this needed since its already done during forward propagation?

        for($i = 0; $i < $this->hiddenLayers; $i++)
            $this->hiddens[$i][] = 1;

        // FORMULATING THE NESECARY WEIGHT CHANGES GO BELOW

        // Returns an array of how far off our calculated answer was from our target answer.
        for($i = 0; $i < $this->outputNeurons; $i++)
        {
            $error = $targets[$i] - $this->outputs[$i];
            $delta_outputs[$i] = $this->sigmoid($this->outputs[$i], true) * $error;
        }

        $avg_sum = 0;

        // Calculate an error for the weight connections between the output layer and the last hidden layer.
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

        // Calculate an error for each weight connection between hidden layers starting at the last hidden layer and propagating towards the top hidden layer.
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

        // Apply the weight change to the weights between the last hidden layer and the output layer
        for($i = 0; $i < $this->outputNeurons; $i++)
        {
            for($j = 0; $j < $this->hiddenNeurons + 1; $j++)
                $this->weights[$this->hiddenLayers][$j][$i] += $this->learningRate * $delta_outputs[$i] * $this->hiddens[$this->hiddenLayers - 1][$j];

        }

        // Apply the weight change to the weights between hidden layers starting at the bottom most hidden layer and moving upwards toward the top most hidden layer.
        for($i = 0; $i < $this->hiddenLayers - 1; $i++)
        {
            for($j = 0; $j < $this->hiddenNeurons; $j++)
            {
                for($k = 0; $k < $this->hiddenNeurons + 1; $k++)
                    $this->weights[$this->hiddenLayers - 1 - $i][$k][$j] += $this->learningRate * $delta_hiddens[$i][$j] * $this->hiddens[$this->hiddenLayers - 2 - $i][$k];

            }


        }

        // Apply the weight change to the weights between the top most hidden layer and the input layer
        for($i = 0; $i < $this->hiddenNeurons; $i++)
        {
            for($j = 0; $j < $this->inputNeurons + 1; $j++)
            {
                //echo 'input: ' . $inputs[$j] . '<br>';
                $this->weights[0][$j][$i] += $this->learningRate * $delta_hiddens[$this->hiddenLayers - 1][$i] * $inputSet[$j];
            }

        }

    }

    /**
     * Iterates over the training data set n times.
     * It calculates an answer and then backPropagates through to find how far that answer was and fix it a bit
     * @param $targets
     */
    public function train($targets)
    {
        for($i = 0; $i < $this->iterations; $i++)
        {
            //if($i % 1000 == 0)
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

    /**
     * Creates a weights array of dimensions x/y/z = a
     *      where x is layer, y is higher layer, z is lower layer, and a is the weight between the two neurons
     * Each weight is randomly generated from the range (-1, 1)
     */
    private function initWeights()
    {
        $this->weights = [];

        // Randomly generate weights between the input layer and the top most hidden layer
        for($i = 0; $i < $this->hiddenNeurons; $i++)
        {
            for($j = 0; $j < $this->inputNeurons + 1; $j++)
            {
                $randomWeight = 2 * lcg_value() - 1;
                $this->weights[0][$j][$i] = $randomWeight;
            }
        }

        // Randomly generate weights between hidden layers
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

        // Randomly generate weights between the bottom most hidden layer and the output layer
        for($i = 0; $i < $this->outputNeurons; $i++)
        {
            for($j = 0; $j < $this->hiddenNeurons + 1; $j++)
            {
                $randomWeight = 2 * lcg_value() - 1;
                $this->weights[$this->hiddenLayers][$j][$i] = $randomWeight;
            }
        }
    }

    /**
     * Returns the answer array
     * @return array
     */
    public function getOutputs()
    {
        return $this->outputs;
    }

    /**
     * Prints the answer array to the screen using a simple JSON encoding
     */
    public function printOutputs()
    {
        echo json_encode($this->getOutputs()) . '<br><br>';
    }

    /**
     * @param $inputs
     * @return array
     */
    public function calculateOutput($inputs, $round = false)
    {
        $this->forwardPropagation($inputs);
        $outputs = $this->getOutputs();
        if($round)
        {
            for($i = 0; $i < count($outputs); $i++)
            {
                $outputs[$i] = round($outputs[$i]);
            }
        }
        return $outputs;
    }

    public function getWeights()
    {
        return $this->weights;
    }

    public function printWeights()
    {
        echo json_encode($this->getWeights()) . '<br><br>';
    }

    /**
     * Finds the average answer array for the input set over n iterations.
     * This must retrain the neural network for each iteration and thus this is very costly. Try not to use
     *  more than 100 iterations.
     * Also prints the average answers array afterwards
     * @param $inputs
     * @param int $iterations
     */
    public function solve($inputs, $iterations = 1)
    {
        $sum = [];
        $outsize = 0;
        for($i = 0; $i < $iterations; $i++)
        {

            // Retrains the neural network
            if($i != 0)
            {
                $this->weights = [];
                $this->hiddens = [];
                $this->outputs = [];
                $this->initWeights();
                $this->train($this->targets);
            }

            // Feeds our input set in question through the neural network to get an answer
            $this->forwardPropagation($inputs);
            $output = $this->getOutputs();

            if($i == 0)
                $outsize = count($output);

            // Calculates the sum for each answer in the answer array.
            for($j = 0; $j < $outsize; $j++)
            {
                if(!isset($sum[$j]))
                    $sum[$j] = 0;
                $sum[$j] += $output[$j];
            }
        }

        // Outputs the average answers from the answer array
        echo '<br>Result: <br>';
        for($j = 0; $j < $outsize; $j++)
            echo '<i>Output ' . ($j + 1) . ':</i> ' . ($sum[$j] / $iterations) . '<br>';
    }
}

/*
 * Training input set data
 */
/*$i = [
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
    [0.3333,0.142857143],
    [0.3333,0.138484974],
    [0.3333,0.128379593],
    [0.3333,0.131754042],
    [0.3333,0.125925553],
    [0.3333,0.117956521],
    [0.3333,0.134852673],
    [0.3333,0.160028165],
];*/

$i2 = [
    [0.266666667, 0.067000000],
    [0.533333333, 0.110000000],
    [0.2, 0.090000000],
    [0.133333333, 0.060000000],
    [0.333333333, 0.055000000],
    [0.2, 0.075000000],
    [0.2, 0.069000000],
    [0.2, 0.088000000],
    [0.133333333, 0.050000000],
    [0.133333333, 0.047500000],
    [0.133333333, 0.150000000],
    [0.266666667, 0.100000000],
    [0.266666667, 0.084800000],
    [0.333333333, 0.110000000],
    [0.4, 0.200000000],
    [0.4, 0.125000000],
    [0.466666667, 0.500500000]
];

/*
 * Training target set data
 */
/*$t = [
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
    [0],
    [0],
    [1],
    [1],
    [1],
    [1],
    [0],
    [0],
];*/

$t2 = [
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
];

$i = [
    [0,0,0,1,0,1,0,0,0,1,0,0],
    [0,1,1,0,0,1,0,0,0,1,0,1],
    [0,0,0,1,0,0,1,0,0,1,0,0],
    [0,1,0,0,0,1,1,0,0,1,1,0],
    [0,0,0,1,0,1,0,0,0,0,1,1],
    [0,0,0,1,0,0,0,1,0,1,0,0],
    [0,0,0,1,0,0,0,1,0,0,0,1],
    [0,0,0,1,0,1,1,0,0,1,0,0],
    [0,1,1,0,0,0,1,0,0,1,0,0],
    [0,0,0,1,0,1,0,1,0,1,0,0],
    [0,1,0,0,0,1,0,0,0,1,0,1],
    [0,1,0,0,0,1,0,1,0,1,0,1],
    [0,0,1,0,0,1,0,0,0,1,0,1],
    [0,1,0,0,0,0,1,1,0,1,1,0],
    [0,1,0,0,0,1,1,0,0,1,0,1],
    [0,1,0,0,0,1,0,1,0,0,0,1],
    [0,0,1,1,0,1,0,1,0,0,0,1],
    [0,1,0,1,0,0,0,1,0,1,0,0],
    [0,0,0,1,0,0,0,1,0,1,1,0],
    [0,1,1,0,0,1,0,1,0,0,0,1],
    [0,0,0,1,0,0,0,1,0,1,0,1],
    [0,0,1,1,0,1,0,1,0,1,1,0],
    [0,0,0,1,0,1,0,0,0,1,0,1],
    [0,0,0,1,0,1,0,0,0,1,0,1],
    [0,0,0,1,0,1,0,0,0,1,0,1],
    [0,0,0,1,0,1,0,0,0,1,0,1],
    [0,0,0,1,0,0,0,1,0,0,0,1],
    [0,1,0,0,0,1,0,1,0,1,0,1],
    [0,0,0,1,0,0,1,0,0,1,1,0],
    [0,0,0,1,0,1,0,1,0,0,0,1],
    [0,1,0,0,0,1,0,0,0,1,0,1],
    [0,0,0,1,0,1,0,1,0,1,0,0],

];

for($go = 0; $go < count($i); $go++)
    $i[$go][] = lcg_value();

echo json_encode($i);

$t = [
    [0,1,0,1],
    [0,1,0,1],
    [0,1,0,1],
    [0,1,0,1],
    [0,1,1,0],
    [0,1,0,1],
    [0,1,0,1],
    [0,1,0,1],
    [0,1,0,1],
    [0,1,0,1],
    [0,0,0,1],
    [0,1,1,0],
    [0,0,0,1],
    [0,1,0,1],
    [0,0,0,1],
    [0,0,1,0],
    [0,1,1,0],
    [0,0,1,1],
    [0,1,0,0],
    [0,1,0,0],
    [0,0,1,0],
    [0,1,1,1],
    [0,1,1,0],
    [0,1,0,1],
    [0,0,0,1],
    [0,1,0,1],
    [0,1,1,0],
    [0,0,0,1],
    [0,1,0,1],
    [0,1,0,0],
    [0,1,0,1],
    [0,1,1,0],
];

$w = json_decode('[[[0.50177789474984,-0.27141342375141,-0.010336332056808,0.9027812850545,0.3084811664135],[-3.6288542376285,-7.368456819728,2.2864658627722,0.40948609804751,0.99965204807573],[17.359912055724,-4.3492091913401,-9.4465652305821,0.54683851974362,-11.16010833178],[6.0566300720508,1.0040004747279,-8.6860634990737,4.4799868730146,1.9240657062036],[-0.043660370643642,0.46001504057024,0.81421097376882,0.94816991315767,0.26556063353862],[4.53530008491,-2.1051461418482,4.5922174388671,-8.2101259386904,-12.380306201317],[-2.3361777194554,1.5921670643469,1.5693109887299,3.7678313353757,-6.5211144363107],[-0.039944746503996,7.0622317614777,-16.885550303462,-11.134155746465,14.597772750743],[0.63374373485682,0.51907184297005,-0.076334849901202,0.29484239862796,-0.8908989530122],[-10.954154237503,-7.8467963847965,-0.84055217157359,-3.3641284939317,-19.460437671006],[9.5729095603851,3.2506284257703,-15.386940648426,-11.268415664948,-2.5081421943277],[-11.460964810456,-2.5417597505777,9.3960789432435,-2.016834468363,10.949770288626],[17.69041469385,-4.2403148485915,50.020229326579,21.197473345642,32.589403767938],[5.0594564813195,1.7332837471311,-5.6351613703936,1.6959990595761,-1.4155729897815]],[[-2.1858952765283,23.039835380894,-12.514775001113,12.819642752648],[-0.91371716909841,12.94802847383,-4.2331924698049,-14.969549128678],[-2.8006620928927,22.511576453229,13.166646284147,-23.181358870857],[-2.0184121765419,-14.917133669366,-36.759948427458,24.564965486613],[-1.5349896005869,-11.13962521745,26.078845354252,-24.341014126987],[-5.663782245009,-9.7705850046979,-6.758200363292,14.624404051537]]]
');

$iteration = 50000;

$debugs = 0;

echo '<ul>';

for($loop = 0; $loop < 1; $loop++)
{
    $nn = new NeuralNetwork($i, $t, $iteration, $w);

    echo '<li>Iterations: ' . $iteration . '<br>';

    //$nn->solve(
    //    [0.3333, 0.833333333], 100
    //);

    echo json_encode($nn->calculateOutput(
            [0,1,0,1,0,1,1,0,0,1,0,1, lcg_value()]
            , true)) . '<br><br>';

    $nn->printWeights();

    $debugs += $nn->debugCounter;

    echo '</li><hr>';

    $nn = null;

    $iteration += 10000;
}

echo '</ul>';

echo '<br><br>' . 'Debug Counter: ' . $debugs . '<br>';
//$nn->forwardPropagation([0.1, 0.000000064]);
//$nn->printOutputs();
