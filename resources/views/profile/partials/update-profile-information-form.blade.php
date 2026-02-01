<section>
    <p class="text-muted">
        {{ __("Update your account's profile information and email address.") }}
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-3" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-2">
                            <p class="text-sm text-warning">
                                {{ __('Your email address is unverified.') }}
                                <button form="send-verification" class="btn btn-link p-0 text-primary">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>
                            @if (session('status') === 'verification-link-sent')
                                <p class="text-sm text-success">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="phone">{{ __('Phone') }}</label>
                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="date_of_birth">{{ __('Date of Birth') }}</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                    @error('date_of_birth')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="address">{{ __('Address') }}</label>
            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $user->address) }}</textarea>
            @error('address')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="profile_picture">{{ __('Profile Picture') }}</label>
            @if($user->profile_picture)
                <div class="mb-2">
                    <img src="{{ \Storage::disk('do_spaces')->url($user->profile_picture) }}" alt="Profile Picture" class="profile-picture-preview">
                </div>
            @endif
            <div class="custom-file">
                <input type="file" id="profile_picture" name="profile_picture" class="custom-file-input @error('profile_picture') is-invalid @enderror" accept="image/*">
                <label class="custom-file-label" for="profile_picture">Choose file</label>
                @error('profile_picture')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <small class="form-text text-muted">Max file size: 2MB. Formats: JPG, PNG, GIF</small>
        </div>

        <hr>
        <h5 class="mb-3">{{ __('Bank Information (Optional)') }}</h5>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="bank_name">{{ __('Bank Name') }}</label>
                    <input type="text" id="bank_name" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $user->bank_name) }}">
                    @error('bank_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="bank_account_holder">{{ __('Account Holder Name') }}</label>
                    <input type="text" id="bank_account_holder" name="bank_account_holder" class="form-control @error('bank_account_holder') is-invalid @enderror" value="{{ old('bank_account_holder', $user->bank_account_holder) }}">
                    @error('bank_account_holder')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="bank_account_number">{{ __('Account Number') }}</label>
                    <input type="text" id="bank_account_number" name="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror" value="{{ old('bank_account_number', $user->bank_account_number) }}">
                    @error('bank_account_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="bank_routing_number">{{ __('Routing Number') }}</label>
                    <input type="text" id="bank_routing_number" name="bank_routing_number" class="form-control @error('bank_routing_number') is-invalid @enderror" value="{{ old('bank_routing_number', $user->bank_routing_number) }}">
                    @error('bank_routing_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="bank_swift_code">{{ __('SWIFT/BIC Code') }}</label>
                    <input type="text" id="bank_swift_code" name="bank_swift_code" class="form-control @error('bank_swift_code') is-invalid @enderror" value="{{ old('bank_swift_code', $user->bank_swift_code) }}">
                    @error('bank_swift_code')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('Save Changes') }}
            </button>
            
            @if (session('status') === 'profile-updated')
                <span class="text-success ml-3">
                    <i class="fas fa-check-circle"></i> {{ __('Saved.') }}
                </span>
            @endif
        </div>
    </form>
</section>

@push('js')
<script>
    // Update file input label with selected filename
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
