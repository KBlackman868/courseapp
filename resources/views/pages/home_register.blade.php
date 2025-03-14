    <!DOCTYPE html>
    <html lang="en" class="h-full">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>Cool Registration</title>
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
        /* Dynamic gradient background */
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center">
        <!-- Glassmorphism container -->
        <div class="bg-white/80 backdrop-blur-xl shadow-2xl rounded-3xl p-10 max-w-lg w-full">
        <form class="space-y-8" method="POST" action="{{ route('register.submit') }}">
            @csrf
            <!-- Email Field -->
            <div class="relative z-0 w-full group">
            <input
                type="email"
                name="email"
                id="email"
                placeholder=" "
                required
                class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:border-indigo-600 peer transition-all duration-300"
            />
            <label
                for="email"
                class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0"
            >
                Email address
            </label>
            </div>
            <!-- Password Field -->
            <div class="relative z-0 w-full group">
            <input
                type="password"
                name="password"
                id="password"
                placeholder=" "
                required
                class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:border-indigo-600 peer transition-all duration-300"
            />
            <label
                for="password"
                class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0"
            >
                Password
            </label>
            </div>
            <!-- Confirm Password Field -->
            <div class="relative z-0 w-full group">
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                placeholder=" "
                required
                class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:border-indigo-600 peer transition-all duration-300"
            />
            <label
                for="password_confirmation"
                class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0"
            >
                Confirm password
            </label>
            </div>
            <!-- First and Last Name -->
            <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full group">
                <input
                type="text"
                name="first_name"
                id="first_name"
                placeholder=" "
                required
                class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:border-indigo-600 peer transition-all duration-300"
                />
                <label
                for="first_name"
                class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0"
                >
                First name
                </label>
            </div>
            <div class="relative z-0 w-full group">
                <input
                type="text"
                name="last_name"
                id="last_name"
                placeholder=" "
                required
                class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:border-indigo-600 peer transition-all duration-300"
                />
                <label
                for="last_name"
                class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0"
                >
                Last name
                </label>
            </div>
            </div>
            <!-- Department Field -->
            <div class="relative z-0 w-full group">
            <input
                type="text"
                name="department"
                id="department"
                placeholder=" "
                required
                class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:border-indigo-600 peer transition-all duration-300"
            />
            <label
                for="department"
                class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0"
            >
                Department (Admin)
            </label>
            </div>
            <!-- Submit Button -->
            <button
            type="submit"
            class="w-full py-3 px-5 text-lg font-semibold text-white bg-indigo-600 rounded-lg shadow-lg hover:bg-indigo-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
            Register
            </button>
        </form>
        </div>
    </body>
    </html>
