<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
        return [
            'condition_id' => ['required'],
            'name' => ['required'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'max:255'],
            'img_url' => ['required', 'mimes:png,jpeg'],
            'category_id' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'condition_id.required' => '商品の状態を選択してください',
            'name.required' => '商品名を入力してください',
            'price.required' => '販売価格を入力してください',
            'price.numeric' => '販売価格は0円以上の数字で入力してください',
            'price.min' => '販売価格は0円以上の数字で入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'img_url.required' => '商品画像を選択してください',
            'img_url.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'category_id.required' => 'カテゴリーを選択してください',
        ];
    }
}
