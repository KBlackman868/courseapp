{{-- resources/views/admin/roles/edit.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Edit Role: {{ $role->display_name ?? $role->name }}</h1>
                <div class="page-actions">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Roles
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Role Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Role Name (System Name)</label>
                                    <input type="text" class="form-control" value="{{ $role->name }}" readonly>
                                    <small class="form-text text-muted">System name cannot be changed</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Display Name <span class="text-danger">*</span></label>
                                    <input type="text" name="display_name" 
                                           class="form-control @error('display_name') is-invalid @enderror" 
                                           value="{{ old('display_name', $role->display_name) }}" required>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $role->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Permissions Selection --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Manage Permissions</h3>
                        <div class="card-tools">
                            <span class="badge badge-info">
                                <span id="selectedCount">{{ count($rolePermissions) }}</span> permissions selected
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach($permissions as $category => $categoryPermissions)
                            <div class="permission-category mb-4">
                                <h5 class="text-capitalize border-bottom pb-2">
                                    <i class="fas fa-folder"></i> {{ str_replace('_', ' ', $category) }}
                                    <button type="button" class="btn btn-sm btn-outline-primary float-right"
                                            onclick="toggleCategory('{{ $category }}')">
                                        Toggle All
                                    </button>
                                </h5>
                                <div class="row">
                                    @foreach($categoryPermissions as $permission)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" 
                                                       class="custom-control-input permission-checkbox" 
                                                       id="perm_{{ $permission->id }}"
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}"
                                                       data-category="{{ $category }}"
                                                       {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="perm_{{ $permission->id }}">
                                                    <strong>{{ $permission->name }}</strong>
                                                    @if($permission->description)
                                                        <br>
                                                        <small class="text-muted">{{ $permission->description }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update Role
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleCategory(category) {
    const checkboxes = $(`.permission-checkbox[data-category="${category}"]`);
    const allChecked = checkboxes.filter(':checked').length === checkboxes.length;
    checkboxes.prop('checked', !allChecked);
    updateCount();
}

function updateCount() {
    const count = $('.permission-checkbox:checked').length;
    $('#selectedCount').text(count);
}

$(document).ready(function() {
    $('.permission-checkbox').on('change', updateCount);
});
</script>
@endpush
@endsection