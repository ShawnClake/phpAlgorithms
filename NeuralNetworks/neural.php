<?php

$outputNeurons = 1;
$inputNeurons = 2;
$hiddenNeurons = 2;

$setCount = 4; // How many training sets we have

$inputs = [
	[1,0],
	[0,1],
	[0,0],
	[1,1]
]; // The inputs we are training with
$targets = [
	[1],
	[1],
	[0],
	[0]
]; // What we are training with; the expected result

$weights = []; // Weights array. Each outer array is for a layer
$hiddens = []; // What the narual network calculates at each hidden neuron
$outputs = []; // What the neural network calculates as an output

$iterations = 5000;
$learningRate = 0.7;

function sigmoid($x, $deriv = false)
{
	if ($deriv)
        return $x * (1 - $x);

    return 1 / (1 + exp(-$x));
}

function forwardPropogation($input, $hiddenNeurons, $inputNeurons, $outputNeurons, &$hiddens, &$outputs, $weights)
{
	$input[] = 1;
	
	for($i = 0; $i < $hiddenNeurons; $i++)
	{
		$sum = 0;
		for($j = 0; $j < $inputNeurons + 1; $j++)
			$sum += $input[$j] * $weights[0][$j][$i];	

		$hiddens[$i] = sigmoid($sum);	 
	}
	
	$hiddens[] = 1;
	
	for($i = 0; $i < $outputNeurons; $i++)
	{
		$sum = 0;
		for($j = 0; $j < $hiddenNeurons + 1; $j++)
			$sum += $hiddens[$j] * $weights[1][$j][$i];
		 
		$outputs[$i] = sigmoid($sum);
	}
}

function train_network($hiddenNeurons, $inputNeurons, $outputNeurons, $outputs, $inputs, $hiddens, $learningRate, &$weights, $targets, $print_error = false)
{
	$delta_hidden = [];
	$delta_outputs = [];
	
	$inputs[] = 1;
	$hiddens[] = 1;
	
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
			$error += $weights[1][$i][$j] * $delta_outputs[$j];
		
		$delta_hidden[$i] = sigmoid($hiddens[$i], true) * $error;

		if($print_error)
			$avg_sum += $error;
	}
	
	if($print_error)
		echo 'Error: ' . $avg_sum / ($hiddenNeurons + 1) . '<br><br>';
	
	for($i = 0; $i < $outputNeurons; $i++)
	{
		for($j = 0; $j < $hiddenNeurons + 1; $j++)
			$weights[1][$j][$i] += $learningRate * $delta_outputs[$i] * $hiddens[$j];
		
	}
	
	for($i = 0; $i < $hiddenNeurons; $i++)
	{
		for($j = 0; $j < $inputNeurons + 1; $j++)
			$weights[0][$j][$i] += $learningRate * $delta_hidden[$i] * $inputs[$j];
	}

}

function train($inputs, $targets, $iterations, $setCount, $hiddenNeurons, $inputNeurons, $outputNeurons, &$hiddens, &$outputs, &$weights, $learningRate)
{
	for($i = 0; $i < $iterations; $i++)
	{
		for($j = 0; $j < $setCount; $j++)
		{
			$input_set = $inputs[$j];
			$target_set = $targets[$j];
			
			forwardPropogation($input_set, $hiddenNeurons, $inputNeurons, $outputNeurons, $hiddens, $outputs, $weights);
			
			if($i % 1000 == 0)
			{
				$print_error = true;
				echo 'Printing errors for iteration ' . $i . '<br>';
			}
			else
				$print_error = false;
			
			train_network($hiddenNeurons, $inputNeurons, $outputNeurons, $outputs, $input_set, $hiddens, $learningRate, $weights, $target_set, $print_error);
			
		}
		
	}
	
}


function initWeights($hiddenNeurons, $inputNeurons, $outputNeurons, &$weights)
{
	for($i = 0; $i < $hiddenNeurons; $i++)
	{
		for($j = 0; $j < $inputNeurons + 1; $j++)
		{
			$randomWeight = 2 * lcg_value() - 1;
			$weights[0][$j][$i] = $randomWeight;
		}	
	}
	
	for($i = 0; $i < $outputNeurons; $i++)
	{
		for($j = 0; $j < $hiddenNeurons + 1; $j++)
		{
			$randomWeight = 2 * lcg_value() - 1;
			$weights[1][$j][$i] = $randomWeight;
		}	
	}
}

//echo $hiddenNeurons;

initWeights($hiddenNeurons, $inputNeurons, $outputNeurons, $weights);

echo(json_encode($weights)) . '<br><br>';

train($inputs, $targets, $iterations, $setCount, $hiddenNeurons, $inputNeurons, $outputNeurons, $hiddens, $outputs, $weights, $learningRate);

echo(json_encode($outputs)) . '<br><br>';

echo 'Test data: ' . '<br>';

forwardPropogation([1,1], $hiddenNeurons, $inputNeurons, $outputNeurons, $hiddens, $outputs, $weights);

echo(json_encode($outputs)) . '<br><br>';