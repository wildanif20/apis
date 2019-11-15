<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use JWTAuth;
// use Tymon\JWTAuth\JWTAuth;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ];
    }
}
