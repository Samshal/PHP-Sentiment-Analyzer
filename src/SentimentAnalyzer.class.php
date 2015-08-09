<?php
/**
 * Sentiment-Analyzer
 *
 * Copyright (c) 2015, Samuel Adeshina <samueladeshina73@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Samuel Adeshina nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   sentiment-analyzer
 * @author    Samuel Adeshina <samueladeshina73@gmail.com>
 * @copyright 2015 Samuel Adeshina <samueladeshina73@gmail.com>
 * @since     File available since Release 1.0.0
 *//*

*/
	class SentimentAnalyzer
	{
		protected $arrSentiments = array(),
				  $arrTypes = array("positive", "negative"),
				  $arrWordType = array("positive" => 0, "negative" => 0),
				  $arrSentenceType = array("positive" => 0, "negative" => 0),
				  $cntWord = 0,
				  $cntSentence = 0,
				  $arrBayesDistribution = array("positive" => 0.5, "negative" => 0.5),
				  $arrBayesDifference;

		public function __construct()
		{
			$this->arrBayesDifference = range(-1.0, 1.5, 0.1);
		}

		private function splitSentence($words)
		{
			preg_match_all('/\w+/', $words, $matches);
			return $matches;
		}

		public function insertTestData($testDataLocation, $testDataType, $testDataAmount = 0)
		{
			if (!in_array($testDataType, $this->arrTypes))
			{
				throw new \Exception('Invalid Sentiment Type Encountered: A Sentiment Can Only Be Negative or Positive');
				return false;
			}
			$amountTracker = 0;
			$testData = fopen($testDataLocation, "r");
			while ($testDatum = fgets($testData))
			{
				if ($amountTracker > $testDataAmount && $testDataAmount > 0)
				{
					break;
				}
				else
				{
					$amountTracker++;
					$this->cntSentence += 1;
					$this->arrSentenceType[$testDataType] += 1;
					$words = self::splitSentence($testDatum)[0];
					foreach ($words as $word)
					{
						$this->arrWordType[$testDataType] += 1;
						$this->cntWord += 1;
						if (!isset($this->arrSentiments[$word][$testDataType]))
						{
							$this->arrSentiments[$word][$testDataType] = 0;
						}
						$this->arrSentiments[$word][$testDataType] += 1;
					}
				}
			}
			return true;
		}

		public function analyzeSentence($sentence)
		{
			foreach ($this->arrTypes as $type)
			{
				$this->arrBayesDistribution[$type] = $this->arrSentenceType[$type] / $this->cntSentence;
			}
			$sentimentScores = array('positive', 'negative');
			$words = self::splitSentence($sentence)[0];
			foreach ($this->arrTypes as $type)
			{
				$sentimentScores[$type] = 1;
				foreach($words as $word)
				{
					if (!isset($this->arrSentiments[$word][$type]))
					{
						$tracker = 0;
					}
					else
					{
						$tracker = $this->arrSentiments[$word][$type];
					}
					$sentimentScores[$type] *= ($tracker + 1) / ($this->arrWordType[$type] + $this->cntWord);
				}
				$sentimentScores[$type] *= $this->arrBayesDistribution[$type];
			}
			arsort($sentimentScores);
			
			if (key($sentimentScores) == 'positive')
			{
				$bayesDifference = $sentimentScores['positive'] / $sentimentScores['negative'];
			}
			else
			{
				$bayesDifference = $sentimentScores['negative'] / $sentimentScores['positive'];
			}
			$positivity = $sentimentScores['positive'] / ($sentimentScores['positive'] + $sentimentScores['negative']);
			$negativity = $sentimentScores['negative'] / ($sentimentScores['positive'] + $sentimentScores['negative']);
			if (in_array(round($bayesDifference, 1), $this->arrBayesDifference))
			{
				$sentiment = 'Neutral';
			}
			else
			{
				$sentiment = key($sentimentScores);
			}

			return array('sentiment'=>$sentiment, 'accuracy'=>array('positivity'=>$positivity, 'negativity'=>$negativity));
			
		}

		public function analyzeDocument($documentLocation)
		{
			$documentHandle = fopen($documentLocation, 'r');
			$positivity = 0; $negativity = 0;
			while ($sentence = fgets($documentHandle))
			{
				$sentiment = self::analyzeSentence($sentence);
				if ($sentiment['sentiment'] == 'negative')
				{
					$negativity += 1;
				}
				else if ($sentiment['sentiment'] == 'positive')
				{
					$positivity += 1;
				}
				else
				{
					continue;
				}
			}
			$positivity  = $positivity / ($positivity + $negativity);
			$negativity = $negativity / ($positivity + $negativity);

			if ($positivity > $negativity)
			{
				$sentiment = 'positive';
			}
			else
			{
				$sentiment = 'negative';
			}

			return array('sentiment' => $sentiment, 'accuracy'=>array('positivity'=>$positivity, 'negativity'=>$negativity));
		}
	}
?>