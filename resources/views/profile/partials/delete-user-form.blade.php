<section>
    <p class="text-muted">
        {{ __('As an administrator, you can delete your account. Once deleted, the account will be soft-deleted and can be restored later if needed. All resources and data will be preserved but the account will be inaccessible.') }}
    </p>

    <button type="button" class="btn btn-danger mt-3" data-toggle="modal" data-target="#confirmUserDeletionModal">
        <i class="fas fa-trash"></i> {{ __('Delete My Account') }}
    </button>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" role="dialog" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">
                            {{ __('Are you sure you want to delete your account?') }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="text-muted">
                            {{ __('This will soft-delete your account. The account can be restored by another administrator if needed. Please enter your password to confirm.') }}
                        </p>

                        <div class="form-group mt-3">
                            <label for="password">{{ __('Password') }}</label>
                            <input type="password" id="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="{{ __('Password') }}" required>
                            @error('password', 'userDeletion')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@if($errors->userDeletion->isNotEmpty())
    @push('js')
    <script>
        $(document).ready(function() {
            $('#confirmUserDeletionModal').modal('show');
        });
    </script>
    @endpush
@endif
