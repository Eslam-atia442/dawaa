<?php

namespace App\Http\Requests;


class FileRequest extends BaseRequest
{
    const MAX_FILE_SIZE = 1024 * 1024 * 10; // 10 MB

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {

        return [
            'file' => 'required|' . $this->getTypeValidation() . '|max:' . self::MAX_FILE_SIZE,
            'type' => 'required|string',
            'fileable_id' => 'nullable|integer',
            'fileable_type' => 'nullable|string',
        ];
    }

}
