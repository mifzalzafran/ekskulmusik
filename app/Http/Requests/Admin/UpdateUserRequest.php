<?php

namespace App\Http\Requests\Admin;

use App\Enums\SkillLevel;
use App\Enums\UserRole;
use App\Models\MusicRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Prepare inputs for validation.
     */
    protected function prepareForValidation(): void
    {
        $merges = [
            'is_choir' => $this->boolean('is_choir'),
            'is_active' => $this->boolean('is_active'),
        ];

        // If submitted from web form with talents section, empty array if none checked
        if ($this->has('talents_submitted') && ! $this->has('music_role_ids')) {
            $merges['music_role_ids'] = [];
        }

        $this->merge($merges);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $routeUser = $this->route('user');
        $userId = $routeUser instanceof User ? $routeUser->id : $routeUser;

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'nis' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'nis')->ignore($userId),
            ],
            'class_name' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'is_choir' => ['boolean'],
            'is_active' => ['boolean'],
            'joined_at' => ['nullable', 'date'],
            'password' => [
                'nullable',
                'string',
                Password::min(8)->letters()->numbers(),
            ],

            // Music roles / talents
            'music_role_ids' => ['nullable', 'array'],
            'music_role_ids.*' => [
                'integer',
                Rule::exists('music_roles', 'id')->where('is_active', true),
            ],

            'primary_music_role_id' => ['nullable'],
            'primary_music_role_ids' => ['nullable', 'array'],
            'primary_role_id' => ['nullable'],
            'primary_role_ids' => ['nullable', 'array'],

            'skill_levels' => ['nullable', 'array'],
            'skill_levels.*' => ['nullable', Rule::enum(SkillLevel::class)],

            'music_roles' => ['nullable', 'array'],
            'music_roles.*.id' => [
                'required_with:music_roles',
                'integer',
                Rule::exists('music_roles', 'id')->where('is_active', true),
            ],
            'music_roles.*.is_primary' => ['nullable', 'boolean'],
            'music_roles.*.skill_level' => ['nullable', Rule::enum(SkillLevel::class)],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $this->validatePrimaryTalent($v);
        });
    }

    /**
     * Validate that at most one primary talent is selected.
     */
    protected function validatePrimaryTalent(Validator $validator): void
    {
        $primaryCount = 0;

        $primaryInput = $this->input('primary_music_role_id');
        if (is_array($primaryInput)) {
            $primaryCount += count($primaryInput);
        } elseif (filled($primaryInput)) {
            $primaryCount++;
            if (! is_numeric($primaryInput) || ! MusicRole::where('id', $primaryInput)->where('is_active', true)->exists()) {
                $validator->errors()->add('primary_music_role_id', 'The selected primary talent is invalid.');
            }
        }

        $primaryIds = $this->input('primary_music_role_ids');
        if (is_array($primaryIds)) {
            $primaryCount += count($primaryIds);
        }

        $primaryRoleInput = $this->input('primary_role_id');
        if (is_array($primaryRoleInput)) {
            $primaryCount += count($primaryRoleInput);
        } elseif (filled($primaryRoleInput)) {
            $primaryCount++;
            if (! is_numeric($primaryRoleInput) || ! MusicRole::where('id', $primaryRoleInput)->where('is_active', true)->exists()) {
                $validator->errors()->add('primary_role_id', 'The selected primary talent is invalid.');
            }
        }

        $primaryRoleIds = $this->input('primary_role_ids');
        if (is_array($primaryRoleIds)) {
            $primaryCount += count($primaryRoleIds);
        }

        $musicRoles = $this->input('music_roles');
        if (is_array($musicRoles)) {
            foreach ($musicRoles as $role) {
                if (! empty($role['is_primary'])) {
                    $primaryCount++;
                }
            }
        }

        if ($primaryCount > 1) {
            $validator->errors()->add('primary_music_role_id', 'Only one primary talent is allowed.');
        }

        $roleIds = $this->input('music_role_ids');
        $effectivePrimary = filled($primaryInput) && ! is_array($primaryInput)
            ? $primaryInput
            : (filled($primaryRoleInput) && ! is_array($primaryRoleInput) ? $primaryRoleInput : null);

        if (filled($effectivePrimary)) {
            if (! is_array($roleIds) || empty($roleIds) || ! in_array((int) $effectivePrimary, array_map('intval', $roleIds))) {
                $validator->errors()->add('primary_music_role_id', 'The selected primary talent must be one of the assigned talents.');
            }
        }
    }
}
