<?php


namespace App\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait UpdateModel
{

    /**
     * Accepts an eloquent model, and the current request instance, using the request data to update the
     * fields of the model based on the configuration of the updateFields array.
     *
     * @param Model $model
     * @param  array $data
     * @param  array $updateFields
     */

    protected function updateModelAttributes(Model $model, array $data, array $updateFields): Model
    {
        if (empty($updateFields)) {
            throw new \UnexpectedValueException('The update fields were not configured for the endpoint.');
        }
        foreach ($updateFields as $requestKey => $modelKey) {
            if (!array_key_exists($requestKey,$data)) {
                continue;
            }
            $model->{$modelKey} = $data[$requestKey];
        }
        return $model;
    }
}
