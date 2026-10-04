<!-- TODO(UI) -->
@php
    $isEdit = $isEdit ?? (isset($user) && $user?->exists);
    $isSubmitted = old('_token') !== null;

    $selectedRole = old('role', isset($user) && $user?->role ? ($user->role instanceof \App\Enums\UserRole ? $user->role->value : $user->role) : \App\Enums\UserRole::Member->value);

    $isChoirChecked = $isSubmitted
        ? (bool) old('is_choir')
        : (bool) ($user->is_choir ?? false);

    $isActiveChecked = $isSubmitted
        ? (bool) old('is_active')
        : (bool) ($user->is_active ?? true);

    $joinedAtValue = old('joined_at', isset($user) && $user?->joined_at ? ($user->joined_at instanceof \Carbon\CarbonInterface ? $user->joined_at->format('Y-m-d') : substr($user->joined_at, 0, 10)) : '');
@endphp

@csrf
@if ($isEdit)
    @method('PUT')
@endif

<div>
    <label for="name">Name *</label><br>
    <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required maxlength="100">
    @error('name')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="email">Email *</label><br>
    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required maxlength="255">
    @error('email')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="nis">NIS (Nomor Induk Siswa)</label><br>
    <input type="text" name="nis" id="nis" value="{{ old('nis', $user->nis ?? '') }}" maxlength="30">
    @error('nis')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="class_name">Class Name</label><br>
    <input type="text" name="class_name" id="class_name" value="{{ old('class_name', $user->class_name ?? '') }}" maxlength="30">
    @error('class_name')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="phone">Phone</label><br>
    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}" maxlength="20">
    @error('phone')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="role">Role *</label><br>
    <select name="role" id="role" required>
        @foreach (\App\Enums\UserRole::cases() as $roleOption)
            <option value="{{ $roleOption->value }}" {{ $selectedRole === $roleOption->value ? 'selected' : '' }}>
                {{ $roleOption->label() }}
            </option>
        @endforeach
    </select>
    @error('role')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label>
        <input type="checkbox" name="is_choir" id="is_choir" value="1" {{ $isChoirChecked ? 'checked' : '' }}>
        Choir Member
    </label>
    @error('is_choir')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label>
        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $isActiveChecked ? 'checked' : '' }}>
        Active Account
    </label>
    @error('is_active')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="joined_at">Joined Date</label><br>
    <input type="date" name="joined_at" id="joined_at" value="{{ $joinedAtValue }}">
    @error('joined_at')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="password">Password {{ $isEdit ? '(leave blank to keep current)' : '*' }}</label><br>
    <input type="password" name="password" id="password" {{ $isEdit ? '' : 'required' }}>
    <small>{{ $isEdit ? 'Optional (min 8 characters, letters & numbers)' : 'Required (min 8 characters, letters & numbers)' }}</small>
    @error('password')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<fieldset>
    <legend>Music Talents & Roles</legend>
    <input type="hidden" name="talents_submitted" value="1">

    @error('music_role_ids')
        <div style="color: red;">{{ $message }}</div>
    @enderror
    @error('primary_music_role_id')
        <div style="color: red;">{{ $message }}</div>
    @enderror

    @if (empty($musicRoles) || $musicRoles->isEmpty())
        <p>No active music roles available.</p>
    @else
        <p>Select talents assigned to this user, optionally pick exactly one as primary, and set skill levels:</p>
        
        <div>
            <label>
                <input type="radio"
                       name="primary_music_role_id"
                       value=""
                       {{ ($isSubmitted ? blank(old('primary_music_role_id')) : (!isset($user) || !$user?->primaryMusicRole())) ? 'checked' : '' }}>
                <em>No Primary Talent</em>
            </label>
        </div>
        <br>

        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Assign</th>
                    <th>Talent Name</th>
                    <th>Primary</th>
                    <th>Skill Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($musicRoles as $role)
                    @php
                        $userAssignedRole = isset($user) && $user?->exists ? $user->musicRoles->firstWhere('id', $role->id) : null;

                        $isTalentChecked = $isSubmitted
                            ? in_array($role->id, array_map('intval', (array) old('music_role_ids', [])))
                            : ($userAssignedRole !== null);

                        $isPrimaryChecked = $isSubmitted
                            ? ((string) old('primary_music_role_id') === (string) $role->id)
                            : (bool) ($userAssignedRole?->pivot->is_primary ?? false);

                        $currentSkill = $isSubmitted
                            ? old("skill_levels.{$role->id}")
                            : $userAssignedRole?->pivot->skill_level;
                        if ($currentSkill instanceof \App\Enums\SkillLevel) {
                            $currentSkill = $currentSkill->value;
                        }
                    @endphp
                    <tr>
                        <td align="center">
                            <input type="checkbox"
                                   name="music_role_ids[]"
                                   id="role_{{ $role->id }}"
                                   value="{{ $role->id }}"
                                   {{ $isTalentChecked ? 'checked' : '' }}>
                        </td>
                        <td>
                            <label for="role_{{ $role->id }}"><strong>{{ $role->name }}</strong></label>
                            @if ($role->description)
                                <br><small>{{ $role->description }}</small>
                            @endif
                        </td>
                        <td align="center">
                            <label>
                                <input type="radio"
                                       name="primary_music_role_id"
                                       value="{{ $role->id }}"
                                       {{ $isPrimaryChecked ? 'checked' : '' }}>
                                Primary
                            </label>
                        </td>
                        <td>
                            <select name="skill_levels[{{ $role->id }}]">
                                <option value="">-- Select Skill Level --</option>
                                @foreach (\App\Enums\SkillLevel::cases() as $level)
                                    <option value="{{ $level->value }}" {{ $currentSkill === $level->value ? 'selected' : '' }}>
                                        {{ $level->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error("skill_levels.{$role->id}")
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</fieldset>
<br>

<div>
    <button type="submit">{{ $isEdit ? 'Update User' : 'Create User' }}</button>
</div>
