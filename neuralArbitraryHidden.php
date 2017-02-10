<?php

// Higher level node/layer/neuron refers to closer to input layer
// Lower level node/layer/neuron refers to closer to output layer

$outputNeurons = 1;
$inputNeurons = 2;
$hiddenNeurons = 3;

$hiddenLayers = 2;

$setCount = 4; // How many training sets we have

$inputs = [
	[0.25,0.000014925],
	[0.125,0.000009091],
	[0.3333,0.000011111],
	[0.5,0.000016667],
	[0.2,0.000018182],
	[0.3333,0.000013333],
	[0.3333,0.000014493],
	[0.3333,0.000011364],
	[0.5,0.00002],
	[0.5,0.000021053],
	[0.5,0.000006667],
	[0.25,0.00001],
	[0.25,0.00001792],
	[0.2,0.000009091],
	[0.166666667,0.000005000],
	[0.166666667,0.000008000],
	[0.142857143,0.000001998],
]; // The inputs we are training with
$targets = [
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
]; // What we are training with; the expected result

$weights = []; // Weights array. Each outer array is for a layer. [Layer][HigherLevelNode][LowerLevelNode] = WeightValue
$hiddens = []; // What the narual network calculates at each hidden neuron [Layer][WhichNode] = ValueAtNode
$outputs = []; // What the neural network calculates as an output

$iterations = 10000;
$learningRate = 0.7;

function sigmoid($x, $deriv = false)
{
	if ($deriv)
        return $x * (1 - $x);

    return 1 / (1 + exp(-$x));
}

function forwardPropogation($input, $hiddenNeurons, $inputNeurons, $outputNeurons, &$hiddens, &$outputs, $weights, $hiddenLayers)
{
	// Adds bias neuron to input layer
	$input[] = 1;
	
	// Takes input neurons and propogates it to the first layer of hidden neurons
	for($i = 0; $i < $hiddenNeurons; $i++)
	{
		$sum = 0;
		for($j = 0; $j < $inputNeurons + 1; $j++)
			$sum += $input[$j] * $weights[0][$j][$i];	

		$hiddens[0][$i] = sigmoid($sum);	 
	}
	
	// Adds bias neuron to each hidden layer
	//for($i = 0; $i < $hiddenLayers; $i++)
	$hiddens[0][] = 1;
	
	// Propogates each hidden layer to the next
	for($i = 0; $i < $hiddenLayers - 1; $i++)    // Loops through and calculates the weights BETWEEN hidden layers. Thus one less weight then there are hidden layers
	{		
		for($j = 0; $j < $hiddenNeurons; $j++)   // Loops through the lower hidden layer of neurons
		{
			$sum = 0;
			for($k = 0; $k < $hiddenNeurons + 1; $k++)    // Loops through the higher hidden layer of neurons. Also adds 1 to loop counter because of BIAS node
				$sum += $hiddens[$i][$k] * $weights[$i + 1][$k][$j];   // Creates a sum of HighNeuron * WeightBetweenHighNeuronAndThisNeuron
			
			$hiddens[$i + 1][$j] = sigmoid($sum);
		}
		
		$hiddens[$i + 1][] = 1;
		
	}
	
	// Takes last hidden layer and propogates to the output layer
	for($i = 0; $i < $outputNeurons; $i++)
	{
		$sum = 0;
		for($j = 0; $j < $hiddenNeurons + 1; $j++)
			$sum += $hiddens[$hiddenLayers - 1][$j] * $weights[$hiddenLayers][$j][$i];
		 
		$outputs[$i] = sigmoid($sum);
	}
}

function train_network($hiddenNeurons, $inputNeurons, $outputNeurons, $outputs, $inputs, $hiddens, $learningRate, &$weights, $targets, $print_error = false, $hiddenLayers)
{
	$delta_hiddens = [];
	$delta_outputs = [];
	
	$inputs[] = 1; // Is this needed since its already done during forward propagation?
	for($i = 0; $i < $hiddenLayers; $i++)
		$hiddens[$i][] = 1;
	
	// FORMULATING THE NESECARY WEIGHT CHANGES GO BELOW
	
	for($i = 0; $i < $outputNeurons; $i++)
	{
		$error = $targets[$i] - $outputs[$i];
		$delta_outputs[$i] = sigmoid($outputs[$i], true) * $error;
	}
	
	$avg_sum = 0;
		
	for($i = 0; $i < $hiddenNeurons + 1; $i++)
	{
		$error = 0;
		for($j = 0; $j < $outputNeurons; $j++)
			$error += $weights[$hiddenLayers][$i][$j] * $delta_outputs[$j];
		
		$delta_hiddens[0][$i] = sigmoid($hiddens[$hiddenLayers - 1][$i], true) * $error;

		if($print_error)
			$avg_sum += $error;
	}
	
	// FORMULATING THE NESECARY WEIGHT CHANGES FOR HIDDEN LAYER TO HIDDEN LAYER
	for($i = 0; $i < $hiddenLayers - 1; $i++)
	{
		for($j = 0; $j < $hiddenNeurons + 1; $j++)
		{
			$error = 0;
			for($k = 0; $k < $hiddenNeurons; $k++)
				$error += $weights[$hiddenLayers - 1 - $i][$j][$k] * $delta_hiddens[$i][$k];
			
			$delta_hiddens[$i + 1][$j] = sigmoid($hiddens[$hiddenLayers - 2 - $i][$j], true) * $error;
			
		}		
	}
	
	// DEBUG GO BELOW
	if($print_error)
		echo 'Error: ' . $avg_sum / ($hiddenNeurons + 1) . '<br><br>';
	
	
	// APPLYING THE WEIGHT CHANGES GO BELOW
	for($i = 0; $i < $outputNeurons; $i++)
	{
		for($j = 0; $j < $hiddenNeurons + 1; $j++)
			$weights[$hiddenLayers][$j][$i] += $learningRate * $delta_outputs[$i] * $hiddens[$hiddenLayers - 1][$j];
		
	}
	
	for($i = 0; $i < $hiddenLayers - 1; $i++)
	{
		for($j = 0; $j < $hiddenNeurons; $j++)
		{
			for($k = 0; $k < $hiddenNeurons + 1; $k++)
				$weights[$hiddenLayers - 1 - $i][$k][$j] += $learningRate * $delta_hiddens[$i][$j] * $hiddens[$hiddenLayers - 2 - $i][$k];
			
		}
		
		
	}
	
	for($i = 0; $i < $hiddenNeurons; $i++)
	{
		for($j = 0; $j < $inputNeurons + 1; $j++)
		{
			//echo 'input: ' . $inputs[$j] . '<br>';
			$weights[0][$j][$i] += $learningRate * $delta_hiddens[$hiddenLayers - 1][$i] * $inputs[$j];
		}
			
	}

}

function train($inputs, $targets, $iterations, $setCount, $hiddenNeurons, $inputNeurons, $outputNeurons, &$hiddens, &$outputs, &$weights, $learningRate, $hiddenLayers)
{
	for($i = 0; $i < $iterations; $i++)
	{
		for($j = 0; $j < $setCount; $j++)
		{
			$input_set = $inputs[$j];
			$target_set = $targets[$j];
			
			forwardPropogation($input_set, $hiddenNeurons, $inputNeurons, $outputNeurons, $hiddens, $outputs, $weights, $hiddenLayers);
			
			if($i % 1000 == 0)
			{
				$print_error = true;
				echo 'Printing errors for iteration ' . $i . '<br>';
			}
			else
				$print_error = false;
			
			train_network($hiddenNeurons, $inputNeurons, $outputNeurons, $outputs, $input_set, $hiddens, $learningRate, $weights, $target_set, $print_error, $hiddenLayers);
			
		}
		
	}
	
}


function initWeights($hiddenNeurons, $inputNeurons, $outputNeurons, &$weights, $hiddenLayers)
{
	for($i = 0; $i < $hiddenNeurons; $i++)
	{
		for($j = 0; $j < $inputNeurons + 1; $j++)
		{
			$randomWeight = 2 * lcg_value() - 1;
			$weights[0][$j][$i] = $randomWeight;
		}	
	}
	
	for($i = 0; $i < $hiddenLayers - 1; $i++)
	{
		for($j = 0; $j < $hiddenNeurons; $j++)
		{
			for($k = 0; $k < $hiddenNeurons + 1; $k++)
			{
				$randomWeight = 2 * lcg_value() - 1;
				$weights[$i + 1][$k][$j] = $randomWeight;
			}		
		}	
	}
	
	for($i = 0; $i < $outputNeurons; $i++)
	{
		for($j = 0; $j < $hiddenNeurons + 1; $j++)
		{
			$randomWeight = 2 * lcg_value() - 1;
			$weights[$hiddenLayers][$j][$i] = $randomWeight;
		}	
	}
}

//echo $hiddenNeurons;

initWeights($hiddenNeurons, $inputNeurons, $outputNeurons, $weights, $hiddenLayers);

echo(json_encode($weights)) . '<br><br>';

train($inputs, $targets, $iterations, $setCount, $hiddenNeurons, $inputNeurons, $outputNeurons, $hiddens, $outputs, $weights, $learningRate, $hiddenLayers);

echo(json_encode($outputs)) . '<br><br>';

echo 'Test data: ' . '<br>';

forwardPropogation([0.1,0.000004310], $hiddenNeurons, $inputNeurons, $outputNeurons, $hiddens, $outputs, $weights, $hiddenLayers);

echo(json_encode($outputs)) . '<br><br>';