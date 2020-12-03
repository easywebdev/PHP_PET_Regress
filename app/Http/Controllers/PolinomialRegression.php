<?php


namespace App\Http\Controllers;


use App\Actions\MyValidator;
use Illuminate\Http\Request;

class PolinomialRegression extends Controller
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
    private $A3 = 0;

    /**
     * @var int
     */
    private $A2 = 0;

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
            $sumDeviate += pow((($this->A0 + $this->A1 * $this->X[$i] + $this->A2 * pow($this->X[$i], 2) + $this->A3 * pow($this->X[$i], 3)) - $this->Y[$i]), 2);
            $sumNormalize += pow(($this->Y[$i] - (array_sum($this->Y) / count($this->Y))), 2);
            if($this->Y[$i] != 0)
            {
                $sumDelta += abs(($this->Y[$i] - ($this->A0 + $this->A1 * $this->X[$i] + $this->A2 * pow($this->X[$i], 2) + $this->A3 * pow($this->X[$i], 3))) / $this->Y[$i]);
            }
        }

        $this->R2 = 1 - ($sumDeviate / $sumNormalize);
        $this->Err = ($sumDelta / count($this->X)) * 100;

        // Round data
        $this->R2 = round($this->R2, 9);
        $this->Err = round($this->Err, 4);
    }

    /**
     * @param array $arr
     * @return float|int
     */
    private function calculateDeterminant(array $arr)
    {
        return $arr[0][0] * ($arr[1][1] * $arr[2][2] * $arr[3][3] + $arr[3][1] * $arr[1][2] * $arr[2][3] + $arr[2][1] * $arr[3][2] * $arr[1][3] - $arr[3][1] * $arr[2][2] * $arr[1][3] - $arr[2][1] * $arr[1][2] * $arr[3][3] - $arr[1][1] * $arr[3][2] * $arr[2][3]) -
                $arr[0][1] * ($arr[1][0] * $arr[2][2] * $arr[3][3] + $arr[2][0] * $arr[3][2] * $arr[1][3] + $arr[3][0] * $arr[1][2] * $arr[2][3] - $arr[3][0] * $arr[2][2] * $arr[1][3] - $arr[2][0] * $arr[1][2] * $arr[3][3] - $arr[1][0] * $arr[3][2] * $arr[2][3]) +
                $arr[0][2] * ($arr[1][0] * $arr[2][1] * $arr[3][3] + $arr[2][0] * $arr[3][1] * $arr[1][3] + $arr[3][0] * $arr[1][1] * $arr[2][3] - $arr[3][0] * $arr[2][1] * $arr[1][3] - $arr[3][1] * $arr[2][3] * $arr[1][0] - $arr[3][3] * $arr[2][0] * $arr[1][1]) -
                $arr[0][3] * ($arr[1][0] * $arr[2][1] * $arr[3][2] + $arr[3][0] * $arr[1][1] * $arr[2][2] + $arr[2][0] * $arr[3][1] * $arr[1][2] - $arr[3][0] * $arr[2][1] * $arr[1][2] - $arr[1][0] * $arr[3][1] * $arr[2][2] - $arr[2][0] * $arr[1][1] * $arr[3][2]);
    }

    /**
     * @param array $arr
     * @param array $col
     * @param int $index
     * @return array
     */
    private function replaceColumn(array $arr, array $col, int $index)
    {
        for ($i = 0; $i < count($col); $i++) {
            $arr[$i][$index] = $col[$i];
        }

        return $arr;
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
                'y' => $this->A3 * pow($currentX, 3) + $this->A2 * pow($currentX, 2) + $this->A1 * $currentX + $this->A0,
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
        $workArr = [];
        $workColumn = [];

        $sumX = 0;
        $sumY = 0;
        $sumX2 = 0;
        $sumX3 = 0;
        $sumX4 = 0;
        $sumX5 = 0;
        $sumX6 = 0;
        $sumXY = 0;
        $sumX2Y = 0;
        $sumX3Y = 0;
        $delta = 0;

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

        // Sort data.
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

            // Find all sums
            for ($i = 0; $i < count($this->X); $i++) {
                $sumX = $sumX + $this->X[$i];
                $sumY = $sumY + $this->Y[$i];
                $sumX2 = $sumX2 + pow($this->X[$i], 2);
                $sumX3 = $sumX3 + pow($this->X[$i], 3);
                $sumX4 = $sumX4 + pow($this->X[$i], 4);
                $sumX5 = $sumX5 + pow($this->X[$i], 5);
                $sumX6 = $sumX6 + pow($this->X[$i], 6);
                $sumXY = $sumXY + $this->X[$i] * $this->Y[$i];
                $sumX2Y = $sumX2Y + pow($this->X[$i], 2) * $this->Y[$i];
                $sumX3Y = $sumX3Y + pow($this->X[$i], 3) * $this->Y[$i];
            }

            // create working arrays
            $workArr[0][0] = $sumX3; $workArr[0][1] = $sumX2; $workArr[0][2] = $sumX;  $workArr[0][3] = count($this->X);
            $workArr[1][0] = $sumX4; $workArr[1][1] = $sumX3; $workArr[1][2] = $sumX2; $workArr[1][3] = $sumX;
            $workArr[2][0] = $sumX5; $workArr[2][1] = $sumX4; $workArr[2][2] = $sumX3; $workArr[2][3] = $sumX2;
            $workArr[3][0] = $sumX6; $workArr[3][1] = $sumX5; $workArr[3][2] = $sumX4; $workArr[3][3] = $sumX3;

            $workColumn[0] = $sumY;
            $workColumn[1] = $sumXY;
            $workColumn[2] = $sumX2Y;
            $workColumn[3] = $sumX3Y;

            // calculate coefficients
            $delta = $this->calculateDeterminant($workArr);

            $this->A0 = $this->calculateDeterminant($this->replaceColumn($workArr, $workColumn, 3)) / $delta;
            $this->A1 = $this->calculateDeterminant($this->replaceColumn($workArr, $workColumn, 2)) / $delta;
            $this->A2 = $this->calculateDeterminant($this->replaceColumn($workArr, $workColumn, 1)) / $delta;
            $this->A3 = $this->calculateDeterminant($this->replaceColumn($workArr, $workColumn, 0)) / $delta;

            // Calculate determination
            $this->calculateDetermination();

            // Create interpolated data array
            $interpolateData = $this->generateInterpolation();
        }

        //print_r($inputData);

        return [
            'source'      => $sourceData,
            'interpolate' => $interpolateData,
            'regress'     => ['A3' => $this->A3, 'A2'=>$this->A2, 'A1' => $this->A1, 'A0' => $this->A0, 'R2' => $this->R2, 'regressErr' => $this->Err],
            'err'         => $err,
        ];
    }
}