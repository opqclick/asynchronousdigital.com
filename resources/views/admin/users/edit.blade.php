@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit User</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">User Information</h3>
        </div>
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <!-- Basic Information -->
                <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Basic Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password">
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role_ids">Roles <span class="text-danger">*</span></label>
                            <select class="form-control @error('role_ids') is-invalid @enderror @error('role_ids.*') is-invalid @enderror"
                                    id="role_ids" name="role_ids[]" multiple required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ in_array($role->id, old('role_ids', $userRoleIds ?? [])) ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Select at least one role. Client role must be exclusive.</small>
                            @error('role_ids')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            @error('role_ids.*')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="active_role_id">Active Role Context</label>
                            <select class="form-control @error('active_role_id') is-invalid @enderror"
                                    id="active_role_id" name="active_role_id">
                                <option value="">Auto (keep current/first selected)</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('active_role_id', $user->active_role_id ?? $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('active_role_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <hr>

                <!-- Personal Information -->
                <h5 class="text-primary mb-3"><i class="fas fa-calendar"></i> Personal Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   id="date_of_birth" name="date_of_birth" 
                                   value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                            @error('date_of_birth')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="joining_date">Joining Date</label>
                            <input type="date" class="form-control @error('joining_date') is-invalid @enderror" 
                                   id="joining_date" name="joining_date" 
                                   value="{{ old('joining_date', $user->joining_date ? $user->joining_date->format('Y-m-d') : '') }}">
                            @error('joining_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="profile_picture">Profile Picture</label>
                            @if($user->profile_picture)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                         alt="Profile" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('profile_picture') is-invalid @enderror" 
                                       id="profile_picture" name="profile_picture" accept="image/*">
                                <label class="custom-file-label" for="profile_picture">Choose file</label>
                            </div>
                            <small class="form-text text-muted">Max 2MB (JPEG, PNG, JPG, GIF). Upload new to replace.</small>
                            @error('profile_picture')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="documents">Documents</label>
                            @if($user->documents && count($user->documents) > 0)
                                <div class="mb-2">
                                    <small class="d-block text-muted">Existing: {{ count($user->documents) }} file(s)</small>
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('documents.*') is-invalid @enderror" 
                                       id="documents" name="documents[]" multiple 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <label class="custom-file-label" for="documents">Choose files</label>
                            </div>
                            <small class="form-text text-muted">Max 5MB per file. New files will be added to existing ones.</small>
                            @error('documents.*')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Bank Details -->
                <h5 class="text-primary mb-3"><i class="fas fa-university"></i> Bank Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                   id="bank_name" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}">
                            @error('bank_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bank_account_holder">Account Holder Name</label>
                            <input type="text" class="form-control @error('bank_account_holder') is-invalid @enderror" 
                                   id="bank_account_holder" name="bank_account_holder" value="{{ old('bank_account_holder', $user->bank_account_holder) }}">
                            @error('bank_account_holder')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bank_account_number">Account Number</label>
                            <input type="text" class="form-control @error('bank_account_number') is-invalid @enderror" 
                                   id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $user->bank_account_number) }}">
                            @error('bank_account_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bank_routing_number">Routing Number / IFSC Code</label>
                            <input type="text" class="form-control @error('bank_routing_number') is-invalid @enderror" 
                                   id="bank_routing_number" name="bank_routing_number" value="{{ old('bank_routing_number', $user->bank_routing_number) }}">
                            @error('bank_routing_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bank_swift_code">SWIFT / BIC Code</label>
                            <input type="text" class="form-control @error('bank_swift_code') is-invalid @enderror" 
                                   id="bank_swift_code" name="bank_swift_code" value="{{ old('bank_swift_code', $user->bank_swift_code) }}">
                            @error('bank_swift_code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Payment Information -->
                <h5 class="text-primary mb-3"><i class="fas fa-dollar-sign"></i> Payment Information</h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_model">Payment Model</label>
                            <select class="form-control @error('payment_model') is-invalid @enderror" 
                                    id="payment_model" name="payment_model">
                                <option value="">Select Payment Model</option>
                                <option value="hourly" {{ old('payment_model', $user->payment_model) == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                <option value="fixed" {{ old('payment_model', $user->payment_model) == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="monthly" {{ old('payment_model', $user->payment_model) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                            @error('payment_model')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="monthly_salary">Monthly Salary</label>
                            <input type="number" step="0.01" class="form-control @error('monthly_salary') is-invalid @enderror" 
                                   id="monthly_salary" name="monthly_salary" value="{{ old('monthly_salary', $user->monthly_salary) }}">
                            @error('monthly_salary')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="teams">Assign to Teams <span class="text-danger">*</span></label>
                    <select class="form-control select2 @error('teams') is-invalid @enderror @error('teams.*') is-invalid @enderror"
                            id="teams" name="teams[]" multiple required>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ in_array($team->id, old('teams', $userTeams)) ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Select at least one team for this user.</small>
                    @error('teams')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                    @error('teams.*')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_active" 
                               name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@1.1.2/dist/css/bootstrap-multiselect.css" />
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@1.1.2/dist/js/bootstrap-multiselect.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Select teams'
            });

            $('#role_ids').multiselect({
                nonSelectedText: 'Select roles',
                nSelectedText: 'roles selected',
                allSelectedText: 'All roles selected',
                buttonWidth: '100%',
                includeSelectAllOption: false,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 300
            });
            
            // Initialize custom file input
            bsCustomFileInput.init();
        });
    </script>
@stop
