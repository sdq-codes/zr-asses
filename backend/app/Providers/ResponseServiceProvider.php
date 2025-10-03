<?php

namespace App\Providers;

use App\Support\Enums\responses\InternalResponseCodes;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{

    public function boot(ResponseFactory $factory): void
    {
        $factory->macro('created', function ($message = '', $code = InternalResponseCodes::CREATED_SUCCESS, $data = null, $key = 'data') use ($factory) {
            $format = [
                'status' => 201,
                'internal_response_code' => $code,
                'title' => $message,
                $key =>  $data
            ];
            return $factory->json($format);
        });

        $factory->macro('error', function ($errors, $httpStatus = 400, $message = '', $code = InternalResponseCodes::EXCEPTION, $key = 'errors') use ($factory) {
            $format = [
                'status' => $httpStatus,
                'internal_response_code' => $code,
                'title' => $message,
                $key =>  $errors
            ];
            return $factory->json($format);
        });

        $factory->macro('updated', function (string $message = '', $code = InternalResponseCodes::UPDATED_SUCCESS, $data = null, $key = 'data') use ($factory){
            $format = [
                'status' => 200,
                'internal_response_code' => $code,
                'title' => $message,
                $key =>  $data
            ];

            return $factory->json($format);
        });

        $factory->macro('fetched', function (string $message = '', $code = InternalResponseCodes::FETCHED_SUCCESS, $data = null, $key = 'data') use ($factory){
            $format = [
                'status' => 200,
                'internal_response_code' => $code,
                'title' => $message,
                $key =>  $data
            ];

            return $factory->json($format);
        });

        $factory->macro('deleted', function (string $message = '', $code = InternalResponseCodes::DELETED_SUCCESS) use ($factory){
            $format = [
                'status' => 200,
                'internal_response_code' => $code,
                'title' => $message,
            ];

            return $factory->json($format);
        });
    }

    public function register(){

    }

}
