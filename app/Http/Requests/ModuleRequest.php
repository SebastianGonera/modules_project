<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class ModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'width'=>[
                'required',
                'regex:/^(auto|inherit|initial|calc\([^()]*\)|\d+(\.\d+)?(px|cm|mm|in|pt|pc|em|rem|vh|vw|vmin|vmax|%))$/',
            ],
            'height'=>[
                'required',
                'regex:/^(auto|inherit|initial|calc\([^()]*\)|\d+(\.\d+)?(px|cm|mm|in|pt|pc|em|rem|vh|vw|vmin|vmax|%))$/',
            ],
            'color' => [
                'required',
                'regex:/^#[0-9a-fA-F]{6}$|^#[0-9a-fA-F]{3}$/'
            ],
            'link'=> [
                'required',
                'url:http,https'
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
    */
    public function messages(): array
    {
        return [
            'width.regex' => 'Invalid value specified for width. Allowed numeric values with units e.g. px, rem, % or keywords: autop, inherit, initial, calc().',
            'height.regex' => 'Invalid value specified for height. Allowed numeric values with units e.g. px, rem, % or keywords: autop, inherit, initial, calc().',
            'color.regex' => 'Invalid value specified for color. Allowed hexadecimal notation e.g.: #221144.'
        ];
    }
}

