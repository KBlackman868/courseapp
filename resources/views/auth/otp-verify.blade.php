<x-layouts>
    <x-slot:heading>
        Verify Your Identity
    </x-slot:heading>

    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
            <div>
                <h2 class="text-center text-3xl font-extrabold text-gray-900">
                    Verify Your Identity
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter the 6-digit code sent to <strong>{{ $user->email }}</strong>
                </p>
            </div>

            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    {{ session('info') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-sm text-yellow-700">
                    <strong>One-Time Verification:</strong> This is a one-time verification to confirm your identity. 
                    You will not need to enter a code on future logins.
                </p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('auth.otp.verify') }}" id="otp-form">
                @csrf

                <div class="flex justify-center space-x-2" id="otp-inputs">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text" 
                               maxlength="1" 
                               class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none otp-input"
                               pattern="[0-9]"
                               inputmode="numeric"
                               required>
                    @endfor
                </div>

                <input type="hidden" name="otp" id="otp-hidden">

                @error('otp')
                    <p class="text-red-500 text-sm text-center">{{ $message }}</p>
                @enderror

                <div class="text-center text-sm text-gray-600">
                    <p>Code expires in <span id="countdown" class="font-bold text-blue-600">10:00</span></p>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Verify Code
                    </button>
                </div>
            </form>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Didn't receive the code?
                    <button type="button" 
                            id="resend-btn"
                            class="font-medium text-blue-600 hover:text-blue-500 disabled:text-gray-400 disabled:cursor-not-allowed"
                            {{ $remainingResends <= 0 ? 'disabled' : '' }}>
                        Resend ({{ $remainingResends }} remaining)
                    </button>
                </p>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Back to login
                </a>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('otp-hidden');
        const form = document.getElementById('otp-form');
        const resendBtn = document.getElementById('resend-btn');
        const countdown = document.getElementById('countdown');

        // Handle OTP input
        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Move to next input
                if (this.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                updateHiddenInput();
            });

            input.addEventListener('keydown', function(e) {
                // Move to previous input on backspace
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                
                pastedData.split('').forEach((char, i) => {
                    if (inputs[i]) {
                        inputs[i].value = char;
                    }
                });
                
                updateHiddenInput();
                
                if (pastedData.length === 6) {
                    inputs[5].focus();
                }
            });
        });

        function updateHiddenInput() {
            hiddenInput.value = Array.from(inputs).map(i => i.value).join('');
        }

        // Countdown timer (10 minutes)
        let timeLeft = 10 * 60;
        const timer = setInterval(function() {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdown.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdown.textContent = 'Expired';
                countdown.classList.remove('text-blue-600');
                countdown.classList.add('text-red-600');
            }
        }, 1000);

        // Resend OTP
        resendBtn.addEventListener('click', function() {
            this.disabled = true;
            this.textContent = 'Sending...';

            fetch('{{ route("auth.otp.resend") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('New code sent to your email!');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to resend code.');
                    this.disabled = false;
                    this.textContent = 'Resend';
                }
            })
            .catch(error => {
                alert('Error sending code. Please try again.');
                this.disabled = false;
                this.textContent = 'Resend';
            });
        });

        // Focus first input on load
        inputs[0].focus();
    });
    </script>
</x-layouts>