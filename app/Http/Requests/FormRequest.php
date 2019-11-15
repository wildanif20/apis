<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

abstract class FormRequest extends LaravelFormRequest
{
    
    abstract public function authorize();

    abstract public function rules();

    public function validation_message($validasi)
    {
        $error = array();
        foreach ($validasi as $key =>$value) { 
            $error[$key] = $value[0];
        }
        return $error;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $contents = $this->validation_message($errors);
        // dd($contents);
        $status['code'] = 404;
        $validasi = $validator->messages()->toArray();
        // $error = $this->validation_message($validasi);
        //Mengambil array pertama dari validation message
        $first_value = reset($contents);

        //Menampilkan validasi pada respon message
        $status['message'] =$first_value;

        //Menampilkan semua list validasi
        $status['content'] = $validasi;

        //Kembalikan respon status code 404
        throw new HttpResponseException(response()->json($status, JsonResponse::HTTP_NOT_FOUND));
    }
}
