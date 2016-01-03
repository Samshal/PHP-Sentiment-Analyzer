
<div id="test">
	<h3>Enter A Sentence Here To Determine If it Sounds Positive, Negative or Neutral</h3>
	<form action = "<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
		<input type = 'text' id = 'sentence'name='sentence' placeholder='Enter Text'/>
		<input type = 'submit' id='goSentence'/>
	</form>
	<h1>OR</h1>
	<h3>Enter The Location of a text file to analyze its content</h3>
	<form action = "<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
		<input type = 'text' id = 'location' name='location' placeholder = 'Copy and Paste File Path Here' />
		<input type='submit' id='goLocation'/>
	</form>
	<div id="result">
		<?php
			if (isset($_POST['sentence']) || isset($_POST['location']))
			{
				$value = (isset($_POST['sentence'])) ? $_POST['sentence'] : $_POST['location'];
				$type = (isset($_POST['sentence'])) ? 'sentence' : 'location';
				if ($type == 'sentence')
				{
					$result = $sat->analyzeSentence($value);
				}
				else
				{
					$result = $sat->analyzeDocument($value);
				}
			}
			else
			{
				$result = array('sentiment'=>null, 'accuracy'=>array('positivity'=>null, 'negativity'=>null));
			}
		?>
		<center><h2>Analysis Result</h2></center>
		<p> Sentiment: <?php echo $result['sentiment']; ?></p>
		<p> Prob. of Being Positive: <?php echo $result['accuracy']['positivity']; ?></p>
		<p> Prob. of Being Negative: <?php echo $result['accuracy']['negativity']; ?></p>
	</div>
</div>
<div id="sentence1">
<h3>Sentence: <small>"<?php echo $sentence1; ?></small>"</h3>
<h2><small>Sentiment Analysis Score: </small> <?php echo $resultofAnalyzingSentence1; ?></h2>
<table>
	<tr>
		<th>Probability Of Sentence Being Positive</th>
		<th>Probability of Sentence Being Negative</th>
	</tr>
	<tr>
		<td><?php echo $probabilityofSentence1BeingPositive; ?></td>
		<td><?php echo $probabilityofSentence1BeingNegative; ?></td>
	</tr>
</table>
</div>
<div id="sentence2">
<h3>Sentence: <small>"<?php echo $sentence2; ?></small>"</h3>
<h2><small>Sentiment Analysis Score: </small> <?php echo $resultofAnalyzingSentence2; ?></h2>
<table>
	<tr>
		<th>Probability Of Sentence Being Positive</th>
		<th>Probability of Sentence Being Negative</th>
	</tr>
	<tr>
		<td><?php echo $probabilityofSentence2BeingPositive; ?></td>
		<td><?php echo $probabilityofSentence2BeingNegative; ?></td>
	</tr>
</table>
</div>
<div id="document">
<h3>Document: <small>"<?php echo $documentLocation; ?></small>"</h3>
<h2><small>Document Analysis Score: </small> <?php echo $resultofAnalyzingDocument; ?></h2>
<table>
	<tr>
		<th>Probability Of Document Being Positive</th>
		<th>Probability of Document Being Negative</th>
	</tr>
	<tr>
		<td><?php echo $probabilityofDocumentBeingPositive; ?></td>
		<td><?php echo $probabilityofDocumentBeingNegative; ?></td>
	</tr>
</table>
</div>
