<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        // Memastikan kita mendapatkan ID (mengantisipasi jika parameter route berupa objek model)
        $userId = $user instanceof \App\Models\User ? $user->id : $user;

        return [
            'name'          =>  ['required',    'string',   'max:255'],
            'email'         =>  ['required',    'string',   'email',    'max:255',
            // Mengabaikan ID user yang sedang di-Update agar tidak memicu error "Email sudah terdaftar"
            Rule::unique('users', 'email')->ignore($userId),
            ],
            'password'      =>  ['nullable',    'string',   Password::min(8)->letters()->numbers()                ],
            'role'          =>  ['required',    Rule::in(['admin', 'petugas', 'peminjam'])
            ],
            'no_hp'         =>  ['nullable',    'string',   'max:15'],
            'alamat'        =>  ['nullable',    'string'],                
            'foto-profile'  =>  ['nullable',    'image',    'mimes:jpeg,png,jpg',   'max:2048'],
        ];
        
    }
}
