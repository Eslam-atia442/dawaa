<?php

return [
    'file' => [
        'image' => 'image|mimes:png,jpg,jpeg,svg',
        'video' => 'mimes:mp4,ogx,oga,ogv,ogg,webm',
        'mixed' => 'mimes:png,jpg,jpeg,svg,pdf,doc,docx,xls,xlsx,txt,mp4,ogx,oga,ogv,ogg,webm'
    ],
    'string' => [
        'req' => 'required|string|min:3|max:250',
        'null' => 'nullable|string|min:3|max:250',
    ],
    'unique' => 'unique:%s,%s',
    'email' => [
        'req' => 'required|string|email|min:3|max:250|unique:%s,%s,%s',
        'null' => 'nullable|string|email|min:3|max:250|unique:%s,%s,%s',
    ],
    'boolean' => [
        'req' => 'required|boolean',
        'null' => 'nullable|boolean',
    ],
    'url' => [
        'req' => 'required|url',
        'null' => 'nullable|url',
    ],
    'phone' => [
        'req' => 'required|numeric|digits_between:6,20',
        'null' => 'nullable|numeric|digits_between:6,20'
    ],
    'text' => [
        'req' => 'required|string|min:3|max:500',
        'null' => 'nullable|string|min:3|max:500',
    ],
    'long_text' => [
        'req' => 'required|string|min:10|max:10000',
        'null' => 'nullable|string|min:10|max:10000',
    ],
    'model' => [
        'req' => 'required|integer|exists:%s,id',
        'null' => 'nullable|integer|exists:%s,id',
    ],
    'date' => [
        'req' => 'required|date',
        'null' => 'nullable|date',
    ],
    'integer' => [
        'req' => 'required|integer|min:0|max:1000000000',
        'null' => 'nullable|integer|min:0|max:1000000000',
    ],
    'array' => [
        'req' => 'required|array',
        'null' => 'nullable|array',
    ],
    'password' => [
        'req' => 'required|string|min:6|max:250|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        'null' => 'nullable|string|min:6|max:250|confirmed',
    ],
    'double' => [
        'req' => 'required|decimal:0.0,1000000000.0',
        'null' => 'nullable|decimal:0.0,1000000000.0',
    ],
];
