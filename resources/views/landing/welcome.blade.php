<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministry of Education</title>
    
    <!-- Meta SEO -->
    <meta name="title" content="Ministry of Education - Digital Learning Platform">
    <meta name="description" content="Transforming education through innovative digital learning solutions. Join thousands of learners on their journey to excellence.">
    <meta name="robots" content="index, follow">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        /* Custom Animations & Effects */
        @keyframes gradient-x {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.5); }
            50% { box-shadow: 0 0 40px rgba(99, 102, 241, 0.8); }
        }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .gradient-animate {
            background-size: 200% 200%;
            animation: gradient-x 15s ease infinite;
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        .slide-up {
            animation: slide-up 0.6s ease-out;
        }
        
        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .glass-dark {
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Gradient Text */
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f3f4f6;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #6366f1, #8b5cf6);
            border-radius: 5px;
        }
        
        /* Parallax Background */
        .parallax {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        /* Morphing Blob */
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            background: linear-gradient(45deg, #6366f1, #8b5cf6, #ec4899);
            filter: blur(40px);
            animation: morph 8s ease-in-out infinite;
        }
        
        @keyframes morph {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            50% { border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%; }
        }
        
        /* Loading Animation */
        .loader {
            border-top-color: #6366f1;
            animation: spinner 1.5s linear infinite;
        }
        
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50 overflow-x-hidden" x-data="{ mobileMenu: false, scrolled: false }" @scroll.window="scrolled = window.pageYOffset > 50">

    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="loader ease-linear rounded-full border-8 border-gray-200 h-16 w-16"></div>
    </div>

    <!-- Enhanced Navigation -->
    <nav class="fixed w-full top-0 z-40 transition-all duration-500"
         :class="scrolled ? 'glass shadow-xl py-2' : 'bg-transparent py-4'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center space-x-3 group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full blur-lg opacity-60 group-hover:opacity-100 transition-opacity"></div>
                        <img src="https://via.placeholder.com/50" alt="MOE Logo" class="relative h-12 w-12 rounded-full border-2 border-white shadow-lg">
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gradient">Ministry of Education</h1>
                        <p class="text-xs text-gray-600 hidden sm:block">Empowering Future Leaders</p>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Home</a>
                    <a href="#features" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Features</a>
                    <a href="#courses" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Courses</a>
                    <a href="#testimonials" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Success Stories</a>
                    <a href="#contact" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Contact</a>
                </div>
                
                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="px-5 py-2 text-gray-700 hover:text-indigo-600 font-medium transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        Get Started
                    </a>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg x-show="!mobileMenu" class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenu" class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="mobileMenu" x-transition class="md:hidden mt-4 pb-4">
                <div class="flex flex-col space-y-4">
                    <a href="#home" class="text-gray-700 hover:text-indigo-600 font-medium">Home</a>
                    <a href="#features" class="text-gray-700 hover:text-indigo-600 font-medium">Features</a>
                    <a href="#courses" class="text-gray-700 hover:text-indigo-600 font-medium">Courses</a>
                    <a href="#testimonials" class="text-gray-700 hover:text-indigo-600 font-medium">Success Stories</a>
                    <a href="{{ route('login') }}" class="px-5 py-2 bg-gray-100 rounded-lg text-center font-medium">Sign In</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg text-center font-medium">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Parallax -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50">
            <div class="absolute top-20 left-10 w-72 h-72 blob opacity-70"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 blob opacity-70" style="animation-delay: 4s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 blob opacity-50" style="animation-delay: 2s;"></div>
        </div>
        
        <!-- Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div data-aos="fade-up" data-aos-duration="1000">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 mb-6">
                    <span class="w-2 h-2 bg-indigo-600 rounded-full mr-2 animate-pulse"></span>
                    Working with Collaboration with Moodle.
                </span>
            </div>
            
            <h1 data-aos="fade-up" data-aos-delay="100" class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                Transform Your
                <span class="text-gradient"> Educational Journey</span>
            </h1>
            
            <p data-aos="fade-up" data-aos-delay="200" class="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Access L
            </p>
                       
            <!-- Stats -->
            <div data-aos="fade-up" data-aos-delay="400" class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-gradient">50K+</div>
                    <div class="text-gray-600">Active Learners</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gradient">500+</div>
                    <div class="text-gray-600">Expert Instructors</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gradient">1000+</div>
                    <div class="text-gray-600">Courses Available</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gradient">98%</div>
                    <div class="text-gray-600">Success Rate</div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold mb-4">
                    Why Choose Our <span class="text-gradient">Platform?</span>
                </h2>
                <p data-aos="fade-up" data-aos-delay="100" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience education reimagined with cutting-edge technology and personalized learning paths
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Card 1 -->
                <div data-aos="fade-up" data-aos-delay="100" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">AI-Powered Learning</h3>
                        <p class="text-gray-600">Personalized learning paths adapted to your pace and style using advanced AI algorithms.</p>
                    </div>
                </div>
                
                <!-- Feature Card 2 -->
                <div data-aos="fade-up" data-aos-delay="200" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Expert Instructors</h3>
                        <p class="text-gray-600">Learn from industry professionals with years of real-world experience in their fields.</p>
                    </div>
                </div>
                
                <!-- Feature Card 3 -->
                <div data-aos="fade-up" data-aos-delay="300" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-pink-600 to-red-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-r from-pink-600 to-red-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Certified Courses</h3>
                        <p class="text-gray-600">Earn industry-recognized certificates upon completion of your courses.</p>
                    </div>
                </div>
                
                <!-- Feature Card 4 -->
                <div data-aos="fade-up" data-aos-delay="400" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-teal-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-600 to-teal-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Learn Anywhere</h3>
                        <p class="text-gray-600">Access your courses on any device, anytime, with offline download options.</p>
                    </div>
                </div>
                
                <!-- Feature Card 5 -->
                <div data-aos="fade-up" data-aos-delay="500" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-600 to-orange-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-r from-yellow-600 to-orange-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Community Support</h3>
                        <p class="text-gray-600">Join a vibrant community of learners and get help whenever you need it.</p>
                    </div>
                </div>
                
                <!-- Feature Card 6 -->
                <div data-aos="fade-up" data-aos-delay="600" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Track Progress</h3>
                        <p class="text-gray-600">Monitor your learning journey with detailed analytics and progress reports.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Course Showcase -->
    <section id="courses" class="py-20 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold mb-4">
                    Explore Our <span class="text-gradient">Popular Courses</span>
                </h2>
                <p data-aos="fade-up" data-aos-delay="100" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    From beginner to advanced, find the perfect course to advance your career
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Course Card 1 -->
                <div data-aos="zoom-in" data-aos-delay="100" class="group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="relative h-48 bg-gradient-to-r from-indigo-500 to-purple-600">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-30 transition-opacity"></div>
                            <div class="absolute top-4 right-4 bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-xs font-bold">
                                BESTSELLER
                            </div>
                            <img src="https://via.placeholder.com/400x200" alt="Course" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <span class="text-xs font-semibold text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full">Web Development</span>
                                <span class="ml-auto flex items-center text-yellow-500">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    <span class="ml-1 text-sm text-gray-600">4.9</span>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Complete Web Developer Bootcamp</h3>
                            <p class="text-gray-600 mb-4">Master HTML, CSS, JavaScript, React, and Node.js in this comprehensive course.</p>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <img src="https://via.placeholder.com/32" alt="Instructor" class="w-8 h-8 rounded-full">
                                    <span class="text-sm text-gray-600">Dr. Sarah Johnson</span>
                                </div>
                                <span class="text-sm text-gray-500">42 hours</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-900">$89.99</span>
                                <button class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                    Enroll Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Course Card 2 -->
                <div data-aos="zoom-in" data-aos-delay="200" class="group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="relative h-48 bg-gradient-to-r from-purple-500 to-pink-600">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-30 transition-opacity"></div>
                            <div class="absolute top-4 right-4 bg-green-400 text-gray-900 px-3 py-1 rounded-full text-xs font-bold">
                                NEW
                            </div>
                            <img src="https://via.placeholder.com/400x200" alt="Course" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <span class="text-xs font-semibold text-purple-600 bg-purple-100 px-3 py-1 rounded-full">Data Science</span>
                                <span class="ml-auto flex items-center text-yellow-500">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    <span class="ml-1 text-sm text-gray-600">4.8</span>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Data Science & Machine Learning</h3>
                            <p class="text-gray-600 mb-4">Learn Python, TensorFlow, and advanced ML algorithms from scratch.</p>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <img src="https://via.placeholder.com/32" alt="Instructor" class="w-8 h-8 rounded-full">
                                    <span class="text-sm text-gray-600">Prof. Michael Chen</span>
                                </div>
                                <span class="text-sm text-gray-500">56 hours</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-900">$129.99</span>
                                <button class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                    Enroll Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Course Card 3 -->
                <div data-aos="zoom-in" data-aos-delay="300" class="group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="relative h-48 bg-gradient-to-r from-green-500 to-teal-600">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-30 transition-opacity"></div>
                            <div class="absolute top-4 right-4 bg-blue-400 text-gray-900 px-3 py-1 rounded-full text-xs font-bold">
                                TRENDING
                            </div>
                            <img src="https://via.placeholder.com/400x200" alt="Course" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <span class="text-xs font-semibold text-green-600 bg-green-100 px-3 py-1 rounded-full">Digital Marketing</span>
                                <span class="ml-auto flex items-center text-yellow-500">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    <span class="ml-1 text-sm text-gray-600">4.7</span>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Digital Marketing Masterclass</h3>
                            <p class="text-gray-600 mb-4">SEO, Social Media, Google Ads, and Content Marketing strategies.</p>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <img src="https://via.placeholder.com/32" alt="Instructor" class="w-8 h-8 rounded-full">
                                    <span class="text-sm text-gray-600">Emily Rodriguez</span>
                                </div>
                                <span class="text-sm text-gray-500">38 hours</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-900">$79.99</span>
                                <button class="px-6 py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                    Enroll Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <a href="#" class="inline-flex items-center px-8 py-3 bg-white text-indigo-600 rounded-full font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    Browse All Courses
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials with Carousel -->
    <section id="testimonials" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold mb-4">
                    Success <span class="text-gradient">Stories</span>
                </h2>
                <p data-aos="fade-up" data-aos-delay="100" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Hear from our learners who have transformed their careers
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div data-aos="fade-up" data-aos-delay="100" class="bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <img src="https://via.placeholder.com/64" alt="Student" class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <h4 class="text-lg font-bold">Alexandra Chen</h4>
                            <p class="text-gray-600">Software Engineer at Google</p>
                        </div>
                    </div>
                    <div class="flex mb-4 text-yellow-400">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </div>
                    <p class="text-gray-700 italic">"The platform completely transformed my career. I went from a beginner to landing my dream job at Google in just 8 months. The instructors are world-class!"</p>
                </div>
                
                <!-- Testimonial 2 -->
                <div data-aos="fade-up" data-aos-delay="200" class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <img src="https://via.placeholder.com/64" alt="Student" class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <h4 class="text-lg font-bold">Marcus Thompson</h4>
                            <p class="text-gray-600">Data Scientist at Microsoft</p>
                        </div>
                    </div>
                    <div class="flex mb-4 text-yellow-400">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </div>
                    <p class="text-gray-700 italic">"I was skeptical about online learning, but this platform exceeded all my expectations. The AI-powered learning paths kept me engaged and motivated throughout."</p>
                </div>
                
                <!-- Testimonial 3 -->
                <div data-aos="fade-up" data-aos-delay="300" class="bg-gradient-to-br from-green-50 to-teal-50 p-8 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <img src="https://via.placeholder.com/64" alt="Student" class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <h4 class="text-lg font-bold">Priya Patel</h4>
                            <p class="text-gray-600">UX Designer at Apple</p>
                        </div>
                    </div>
                    <div class="flex mb-4 text-yellow-400">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </div>
                    <p class="text-gray-700 italic">"The community support is amazing! Whenever I got stuck, there was always someone ready to help. The courses are practical and industry-relevant."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold text-white mb-6">
                Ready to Transform Your Future?
            </h2>
            <p data-aos="fade-up" data-aos-delay="100" class="text-xl text-indigo-100 mb-8">
                Join thousands of learners who are advancing their careers with our platform. Start your free trial today!
            </p>
            <div data-aos="fade-up" data-aos-delay="200" class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#" class="px-8 py-4 bg-white text-indigo-600 rounded-full font-semibold text-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                    Start Free Trial
                </a>
                <a href="#" class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-full font-semibold text-lg hover:bg-white hover:text-indigo-600 transition-all duration-300">
                    View Pricing
                </a>
            </div>
            <p data-aos="fade-up" data-aos-delay="300" class="text-indigo-100 mt-6">
                No credit card required • 30-day money-back guarantee
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="https://via.placeholder.com/40" alt="Logo" class="h-10 w-10 rounded-full">
                        <span class="text-xl font-bold">Ministry of Education</span>
                    </div>
                    <p class="text-gray-400 mb-4">Empowering learners worldwide through innovative digital education.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/><path d="M12 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a3.999 3.999 0 110-7.998 3.999 3.999 0 010 7.998z"/><circle cx="18.406" cy="5.594" r="1.44"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Courses</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Instructors</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>
                
                <!-- Support -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
                
                <!-- Newsletter -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Stay Updated</h4>
                    <p class="text-gray-400 mb-4">Subscribe to get the latest news and updates</p>
                    <form class="flex">
                        <input type="email" placeholder="Your email" class="flex-1 px-4 py-2 bg-gray-800 text-white rounded-l-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700 transition-colors">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2024 Ministry of Education. All rights reserved. | Developed with ❤️ by Kyle Blackman</p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Remove preloader when page loads
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.getElementById('preloader').style.display = 'none';
            }, 500);
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>