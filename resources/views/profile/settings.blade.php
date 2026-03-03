<x-layouts>
  <x-slot:heading>Settings</x-slot:heading>

  <div class="max-w-2xl mx-auto space-y-6">

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

    {{-- Change Profile Photo --}}
    <div>
      <h3 class="text-lg font-semibold text-base-content mb-3">Update Profile Picture</h3>
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
        <button type="submit" class="btn btn-primary">Upload</button>
      </form>
    </div>

    <div class="divider"></div>

    {{-- Change Password --}}
    <div>
      <h3 class="text-lg font-semibold text-base-content mb-3">Change Password</h3>
      <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
        @csrf
        <div class="form-control">
          <label class="label"><span class="label-text">Current Password</span></label>
          <input type="password" name="current_password" required class="input input-bordered w-full" />
          @error('current_password')
            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
          @enderror
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">New Password</span></label>
          <input type="password" name="password" required class="input input-bordered w-full" />
          @error('password')
            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
          @enderror
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Confirm New Password</span></label>
          <input type="password" name="password_confirmation" required class="input input-bordered w-full" />
        </div>
        <button type="submit" class="btn btn-success">Update Password</button>
      </form>
    </div>

    <div class="divider"></div>

    {{-- Forgot Password Link --}}
    <div>
      <h3 class="text-lg font-semibold text-base-content mb-2">Forgot Your Password?</h3>
      <p class="text-base-content/70 text-sm mb-3">Sign out and use the password reset link on the login page.</p>
      <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm">Sign Out &amp; Reset Password</button>
      </form>
    </div>
  </div>
</x-layouts>
