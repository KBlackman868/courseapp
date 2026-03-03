<x-layouts>
  <x-slot:heading>My Profile</x-slot:heading>

  <div class="max-w-4xl mx-auto space-y-6">

    {{-- Validation Errors --}}
    @if($errors->any())
      <div class="alert alert-error">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
          <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    {{-- Profile Card --}}
    <div class="flex flex-col md:flex-row gap-6 items-start">
      {{-- Avatar & Info --}}
      <div class="flex flex-col items-center text-center md:w-1/3 space-y-3">
        <div class="avatar">
          <div class="w-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
            <img src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . '+' . $user->last_name) . '&background=6366f1&color=fff&size=128' }}"
                 alt="{{ $user->first_name }}"
                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->first_name) }}&background=6366f1&color=fff&size=128'" />
          </div>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-base-content">{{ $user->first_name }} {{ $user->last_name }}</h2>
          <p class="text-base-content/70">{{ $user->email }}</p>
          @if($user->getRoleNames()->isNotEmpty())
            <span class="badge badge-primary mt-2">{{ ucwords(str_replace('_', ' ', $user->getRoleNames()->first())) }}</span>
          @endif
        </div>
      </div>

      {{-- Enrolled Courses --}}
      <div class="flex-1 w-full">
        <h3 class="text-lg font-semibold text-base-content mb-3">Enrolled Courses</h3>
        @if($enrollments->isEmpty())
          <div class="text-center py-8 text-base-content/60">
            <svg class="w-12 h-12 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <p>No approved courses yet.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm mt-3">Browse Courses</a>
          </div>
        @else
          <div class="space-y-2">
            @foreach($enrollments as $enroll)
              <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                <div class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                  <span class="font-medium text-base-content">{{ $enroll->course->title }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="badge badge-sm {{ $enroll->status === 'approved' ? 'badge-success' : ($enroll->status === 'pending' ? 'badge-warning' : 'badge-ghost') }}">
                    {{ ucfirst($enroll->status) }}
                  </span>
                  @if($enroll->status === 'approved' && $enroll->course->moodle_course_id)
                    <a href="{{ route('courses.access-moodle', $enroll->course) }}" class="btn btn-primary btn-xs">Go to Course</a>
                  @elseif($enroll->status === 'approved')
                    <a href="{{ route('courses.show', $enroll->course) }}" class="btn btn-ghost btn-xs">View</a>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <div class="divider"></div>

    {{-- Change Profile Picture --}}
    <div>
      <h3 class="text-lg font-semibold text-base-content mb-3">Change Profile Picture</h3>
      <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-start gap-4">
        @csrf
        <div class="form-control w-full sm:max-w-xs">
          <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/gif,image/webp"
                 class="file-input file-input-bordered file-input-primary w-full" required />
          <label class="label">
            <span class="label-text-alt text-base-content/60">JPG, PNG, GIF, or WebP. Max 2MB.</span>
          </label>
          @error('profile_photo')
            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          Upload Photo
        </button>
      </form>
    </div>

    <div class="divider"></div>

    {{-- Change Password --}}
    <div>
      <h3 class="text-lg font-semibold text-base-content mb-3">Change Password</h3>
      <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 max-w-md">
        @csrf
        <div class="form-control">
          <label class="label"><span class="label-text">Current Password</span></label>
          <input type="password" name="current_password" required class="input input-bordered w-full" placeholder="Enter current password" />
          @error('current_password')
            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
          @enderror
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">New Password</span></label>
          <input type="password" name="password" required class="input input-bordered w-full" placeholder="Enter new password" />
          @error('password')
            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
          @enderror
          <label class="label">
            <span class="label-text-alt text-base-content/60">
              Minimum {{ $user->hasRole(['superadmin', 'admin', 'course_admin', 'moh_staff']) ? '14' : '12' }} characters required.
            </span>
          </label>
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Confirm New Password</span></label>
          <input type="password" name="password_confirmation" required class="input input-bordered w-full" placeholder="Confirm new password" />
        </div>
        <button type="submit" class="btn btn-success">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
          Update Password
        </button>
      </form>
    </div>

    <div class="divider"></div>

    {{-- Forgot / Reset Password --}}
    <div>
      <h3 class="text-lg font-semibold text-base-content mb-2">Forgot Your Password?</h3>
      <p class="text-base-content/70 text-sm mb-3">
        If you can't remember your current password, sign out and use the password reset link on the login page.
        A reset link will be sent to <strong>{{ $user->email }}</strong>.
      </p>
      <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          Sign Out &amp; Reset Password
        </button>
      </form>
    </div>
  </div>
</x-layouts>
