<section>
    <p class="text-muted">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}" class="mt-3">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password">{{ __('Current Password') }} <span class="text-danger">*</span></label>
            <input type="password" id="update_password_current_password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="update_password_password">{{ __('New Password') }} <span class="text-danger">*</span></label>
            <input type="password" id="update_password_password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
            @error('password', 'updatePassword')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
            <input type="password" id="update_password_password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-key"></i> {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success ml-3">
                    <i class="fas fa-check-circle"></i> {{ __('Password updated.') }}
                </span>
            @endif
        </div>
    </form>
</section>
