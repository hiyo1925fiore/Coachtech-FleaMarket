<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
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
            'message' => ['required', 'max:400'],
            'img_url' => ['mimes:png,jpeg'],
        ];
    }

    public function messages()
    {
        return [
            'message.required' => '本文を入力してください',
            'message.max' => '本文は400文字以内で入力してください',
            'img_url.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }

    /**
     * バリデーション失敗時の処理
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // PUTリクエスト（編集）の場合
        if ($this->method() === 'PUT') {
            $exhibitionId = \App\Models\Chat::find($this->route('chatId'))->exhibition_id ?? null;

            $response = redirect()
                ->route('chat.show', $exhibitionId)
                ->withInput()
                ->withErrors($validator)
                ->with([
                    'is_edit_mode' => true,
                    'edit_chat_id' => $this->route('chatId'),
                ]);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        // POSTリクエスト（新規投稿）の場合はデフォルトの処理
        parent::failedValidation($validator);
    }
}
