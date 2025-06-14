<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
        // paymentフィールドが存在する場合のみバリデーション
        if ($this->has('payment') || $this->routeIs('purchase.store')) {
            $rules['payment'] = ['required'];
        }

        // post_code・addressフィールドが存在する場合のみバリデーション
        if ($this->has('post_code','address') || $this->routeIs('itemlist')) {
            $rules = [
                'post_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
                'address' => ['required'],
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'payment.required' => '支払方法を選択してください',
            'post_code.required' => '郵便番号を入力してください',
            'post_code.regex' => '郵便番号は123-4567の形式で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
