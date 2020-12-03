<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Actions\MyValidator;

class LinearRegression extends Controller
{
    /**
     * @var array
     */
    private $X = [];

    /**
     * @var array
     */
    private $Y = [];

    /**
     * @var int
     */
    private $A1 = 0;

    /**
     * @var int
     */
    private $A0 = 0;

    /**
     * @var int
     */
    private $R2 = 0;

    /**
     * @var int
     */
    private $Err = 0;

    /**
     *
     */
    private function calculateDetermination()
    {
        $sumDeviate = 0;
        $sumNormalize = 0;
        $sumDelta = 0;

        for ($i = 0; $i < count($this->X); $i++) {
            $sumDeviate += pow((($this->A0 + $this->A1 * $this->X[$i]) - $this->Y[$i]), 2);
            $sumNormalize += pow(($this->Y[$i] - (array_sum($this->Y) / count($this->Y))), 2);
            if($this->Y[$i] != 0)
            {
                $sumDelta += abs(($this->Y[$i] - ($this->A0 + $this->A1 * $this->X[$i])) / $this->Y[$i]);
            }
        }

        $this->R2 = 1 - ($sumDeviate / $sumNormalize);
        $this->Err = ($sumDelta / count($this->X)) * 100;
    }

    /**
     * @return array
     */
    private function generateInterpolation()
    {
        $points = 100;
        $step = (end($this->X) - $this->X[0]) / $points;
        $interpolateData = [];

        for ($i = 0; $i <= $points; $i++) {
            $currentX = $this->X[0] + $i * $step;
            $interpolateData[$i] = [
                'x' => $currentX,
                'y' => $this->A1 * $currentX + $this->A0,
            ];
        }

        return $interpolateData;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getRegress(Request $request)
    {
        $err = null;
        $sumXY = 0;
        $sumXsqr = 0;
        $sourceData = [];
        $interpolateData = [];
        $removedIndexes = [];

        // Remove empty rows by 'X' empty value
        $inputData = $request->input('inputData');

        for ($i = 0; $i < count($inputData); $i++) {
            if($inputData[$i]['x'] == '<br>' && $inputData[$i]['y'] == '<br>') {
                $removedIndexes[] = $i;
            }
        }

        for ($i = 0; $i < count($removedIndexes); $i++) {
            unset($inputData[$removedIndexes[$i]]);
        }

        // Replace ',' to '.' and remove all '<br>'
        foreach ($inputData as $key => $value) {
            $inputData[$key]['x'] = str_replace(['<br>', ','], ['', '.'], $inputData[$key]['x']);
            $inputData[$key]['y'] = str_replace(['<br>', ','], ['', '.'], $inputData[$key]['y']);
        }

        // Validate data
        $myValidator = new MyValidator();
        $err = $myValidator->validateData(['data' => $inputData], 2);

        // Sort data and create arrays for calculation.
        usort($inputData, function($a, $b) {return strcmp($a['x'], $b['x']);});

        // when data sort operation executed the symbol '<br>' has add to end of the data
        // we have to remove it before next steps
        for ($i = 0; $i < count($inputData); $i++) {
            $this->X[] = str_replace('<br>', '', $inputData[$i]['x']);
            $this->Y[] = str_replace('<br>', '', $inputData[$i]['y']);
        }


        // Calculate regression
        if($err == null) {
            // Create source data array
            for ($i = 0; $i < count($this->X); $i++) {
                $sourceData[$i] = [
                    'x' => $this->X[$i],
                    'y' => $this->Y[$i],
                ];
            }

            // Find sum X*Y and sum X^2
            for ($i = 0; $i < count($this->X); $i++) {
                $sumXY += $this->X[$i] * $this->Y[$i];
                $sumXsqr += pow($this->X[$i], 2);
            }

            // Calculate slope and intercept
            $this->A1 = (count($this->X) * $sumXY - array_sum($this->X) * array_sum($this->Y)) / (count($this->X) * $sumXsqr - pow(array_sum($this->X), 2));
            $this->A0 = (array_sum($this->Y) - $this->A1 * array_sum($this->X)) / count($this->X);

            // Calculate determination
            $this->calculateDetermination();

            // Create interpolated data array
            $interpolateData = $this->generateInterpolation();
        }

        //print_r($inputData);

        return [
            'source'      => $sourceData,
            'interpolate' => $interpolateData,
            'regress'     => ['A1' => $this->A1, 'A0' => $this->A0, 'R2' => $this->R2, 'regressErr' => $this->Err],
            'err'         => $err,
        ];
    }
}