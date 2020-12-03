<?php


namespace App\Actions;
use Illuminate\Support\Facades\Validator;

class MyValidator
{
    /**
     * @param array $data
     * @param $minDataCount
     * @return array
     */
    public function validateData(array $data, $minDataCount)
    {
        $err = null;

        // Add rule for min data count
        $rules = ['data'   => 'required|array|min:' . $minDataCount];

        foreach ($data['data'] as $key => $value) {
            // Create rules
            $rules ['data.' . $key . '.x'] = 'required|numeric';
            $rules ['data.' . $key . '.y'] = 'required|numeric';

            // Create messages
            $messages ['data.' . $key . '.x.numeric'] = 'X[' . ($key + 1) .'] must be a number';
            $messages ['data.' . $key . '.y.numeric'] = 'Y[' . ($key + 1) .'] must be a number';
            $messages ['data.' . $key . '.x.required'] = 'X[' . ($key + 1) .'] is required';
            $messages ['data.' . $key . '.y.required'] = 'Y[' . ($key + 1) .'] is required';
        }

        // Validate
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            foreach (json_decode($validator->messages()) as $msg) {
                $err[] = $msg[0];
            }
        }

        return $err;
    }
}