{{-- resources/views/admin/roles/permissions.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    Permissions for: {{ $role->display_name ?? $role->name }}
                </h1>
                <div class="page-actions">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Roles
                    </a>
                    @if($role->name !== 'superadmin')
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Permissions
                        </a>
                    @endif
                </div>
            </div>

            @if($role->name === 'superadmin')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    The Superadmin role has all permissions by default and cannot be modified.
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assigned Permissions</h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">
                            {{ $role->name === 'superadmin' ? 'All' : $role->permissions->count() }} permissions
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if($role->name === 'superadmin')
                        <div class="text-center py-4">
                            <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                            <h4>Full System Access</h4>
                            <p class="text-muted">This role has unrestricted access to all system features.</p>
                        </div>
                    @elseif($permissions->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                            <h4>No Permissions Assigned</h4>
                            <p class="text-muted">This role currently has no permissions.</p>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary mt-2">
                                Add Permissions
                            </a>
                        </div>
                    @else
                        @foreach($permissions as $category => $categoryPermissions)
                            <div class="permission-group mb-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-folder text-primary"></i>
                                    {{ ucfirst(str_replace('_', ' ', $category)) }}
                                    <span class="badge badge-secondary float-right">
                                        {{ $categoryPermissions->count() }}
                                    </span>
                                </h5>
                                <div class="row">
                                    @foreach($categoryPermissions as $permission)
                                        <div class="col-md-6 mb-2">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                                                <div>
                                                    <strong>{{ $permission->name }}</strong>
                                                    @if($permission->description)
                                                        <br>
                                                        <small class="text-muted">{{ $permission->description }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Users with this role --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Users with this Role</h3>
                </div>
                <div class="card-body">
                    @php
                        $usersWithRole = \App\Models\User::role($role->name)->get();
                    @endphp
                    
                    @if($usersWithRole->isEmpty())
                        <p class="text-muted text-center">No users have been assigned this role yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usersWithRole as $user)
                                        <tr>
                                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->department ?? 'N/A' }}</td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection