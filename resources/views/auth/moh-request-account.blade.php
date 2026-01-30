<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MOH Staff Account Request - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            {{-- Back to Home Link --}}
            <div class="mb-6">
                <a href="{{ route('home') }}" class="inline-flex items-center text-white/70 hover:text-white transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>
            </div>

            {{-- Registration Card --}}
            <div class="card bg-base-100 shadow-2xl">
                <div class="card-body">
                    {{-- Header --}}
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">MOH Staff Account Request</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">
                            Request an account using your @health.gov.tt email
                        </p>
                    </div>

                    {{-- Info Alert --}}
                    <div class="alert alert-info mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm">
                            <strong>Note:</strong> Your account will need to be approved by an administrator before you can log in.
                        </div>
                    </div>

                    {{-- Error Messages --}}
                    @if($errors->any())
                        <div class="alert alert-error mb-6">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Registration Form --}}
                    <form action="{{ route('moh.request-account.submit') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- Name Fields --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">First Name</span>
                                </label>
                                <input type="text"
                                       name="first_name"
                                       value="{{ old('first_name') }}"
                                       class="input input-bordered @error('first_name') input-error @enderror"
                                       placeholder="John"
                                       required />
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Last Name</span>
                                </label>
                                <input type="text"
                                       name="last_name"
                                       value="{{ old('last_name') }}"
                                       class="input input-bordered @error('last_name') input-error @enderror"
                                       placeholder="Doe"
                                       required />
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">MOH Email Address</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="input input-bordered @error('email') input-error @enderror"
                                   placeholder="john.doe@health.gov.tt"
                                   required />
                            <label class="label">
                                <span class="label-text-alt text-gray-500">Must be a @health.gov.tt email address</span>
                            </label>
                        </div>

                        {{-- Department --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Department</span>
                            </label>
                            <input type="text"
                                   name="department"
                                   value="{{ old('department') }}"
                                   class="input input-bordered @error('department') input-error @enderror"
                                   placeholder="e.g., Public Health, Administration"
                                   required />
                        </div>

                        {{-- Phone (Optional) --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Phone Number <span class="text-gray-400">(Optional)</span></span>
                            </label>
                            <input type="tel"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   class="input input-bordered @error('phone') input-error @enderror"
                                   placeholder="+1 (868) XXX-XXXX" />
                        </div>

                        {{-- Password --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Password</span>
                            </label>
                            <input type="password"
                                   name="password"
                                   class="input input-bordered @error('password') input-error @enderror"
                                   placeholder="Minimum 8 characters"
                                   required />
                        </div>

                        {{-- Confirm Password --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Confirm Password</span>
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="input input-bordered"
                                   placeholder="Confirm your password"
                                   required />
                        </div>

                        {{-- Submit Button --}}
                        <div class="form-control mt-6">
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Submit Request
                            </button>
                        </div>
                    </form>

                    {{-- Divider --}}
                    <div class="divider my-6">OR</div>

                    {{-- External User Link --}}
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            Not an MOH Staff member?
                        </p>
                        <a href="{{ route('register.external') }}" class="btn btn-outline btn-sm">
                            Register as External User
                        </a>
                    </div>

                    {{-- Login Link --}}
                    <div class="text-center mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Already have an account?
                            <a href="{{ route('login') }}" class="link link-primary font-semibold">
                                Sign In
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <p class="text-center text-sm text-white/60 mt-6">
                Questions? Contact <a href="mailto:support@health.gov.tt" class="text-white/80 hover:text-white underline">support@health.gov.tt</a>
            </p>
        </div>
    </div>
</body>
</html>
