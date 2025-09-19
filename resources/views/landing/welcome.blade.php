<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministry of Health - Learning Management System</title>
    
    <!-- Meta SEO -->
    <meta name="title" content="Ministry of Health - Learning Management System">
    <meta name="description" content="Strengthening healthcare workforce capability through innovative digital learning solutions. Access clinical applications, computer literacy, and professional development modules.">
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
            0%, 100% { 
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.5),
                           0 0 40px rgba(59, 130, 246, 0.3),
                           0 0 60px rgba(59, 130, 246, 0.1);
            }
            50% { 
                box-shadow: 0 0 30px rgba(59, 130, 246, 0.8),
                           0 0 60px rgba(59, 130, 246, 0.5),
                           0 0 80px rgba(59, 130, 246, 0.3);
            }
        }

        @keyframes pulse-glow-subtle {
            0%, 100% { 
                box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
            }
            50% { 
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.6);
            }
        }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
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

        .pulse-glow-subtle {
            animation: pulse-glow-subtle 2s ease-in-out infinite;
        }
        
        .slide-up {
            animation: slide-up 0.6s ease-out;
        }

        .shimmer {
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }
        
        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .glass-dark {
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .glass-blue {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(99, 102, 241, 0.1));
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        
        /* Gradient Text */
        .text-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gradient-blue {
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f3f4f6;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #3b82f6, #6366f1);
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #2563eb, #4f46e5);
        }
        
        /* Morphing Blob */
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            background: linear-gradient(45deg, #3b82f6, #6366f1, #8b5cf6);
            filter: blur(40px);
            animation: morph 8s ease-in-out infinite;
        }
        
        @keyframes morph {
            0%, 100% { 
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                transform: rotate(0deg);
            }
            50% { 
                border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%;
                transform: rotate(180deg);
            }
        }
        
        /* Loading Animation */
        .loader {
            border-top-color: #3b82f6;
            animation: spinner 1.5s linear infinite;
        }
        
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Hover Effects */
        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
        }

        /* Neon Glow Effect */
        .neon-glow {
            box-shadow: 0 0 5px #3b82f6,
                       0 0 10px #3b82f6,
                       0 0 15px #3b82f6,
                       0 0 20px #3b82f6;
        }

        /* Card Hover Effect */
        .card-hover {
            position: relative;
            overflow: hidden;
        }

        .card-hover::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card-hover:hover::before {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-50 overflow-x-hidden" x-data="{ mobileMenu: false, scrolled: false }" @scroll.window="scrolled = window.pageYOffset > 50">

    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 bg-gradient-to-br from-blue-50 to-indigo-100 z-50 flex items-center justify-center">
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
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur-lg opacity-60 group-hover:opacity-100 transition-opacity pulse-glow-subtle"></div>
                        <img src="{{ asset('images/moh_logo.jpg') }}" alt="Ministry of Health" alt="MOH Logo" class="relative h-12 w-12 rounded-full border-2 border-white shadow-lg">
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gradient-blue">Ministry of Health</h1>
                        <p class="text-xs text-gray-600 hidden sm:block">Learning Management System</p>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-blue-600 font-medium transition-colors relative group">
                        Home
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="#features" class="text-gray-700 hover:text-blue-600 font-medium transition-colors relative group">
                        Features
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="#courses" class="text-gray-700 hover:text-blue-600 font-medium transition-colors relative group">
                        Training Modules
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="#partners" class="text-gray-700 hover:text-blue-600 font-medium transition-colors relative group">
                        Partners
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 font-medium transition-colors relative group">
                        Contact
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"></span>
                    </a>
                </div>
                
                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="px-5 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold hover:shadow-xl transform hover:scale-105 transition-all duration-300 pulse-glow-subtle">
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
                    <a href="#home" class="text-gray-700 hover:text-blue-600 font-medium">Home</a>
                    <a href="#features" class="text-gray-700 hover:text-blue-600 font-medium">Features</a>
                    <a href="#courses" class="text-gray-700 hover:text-blue-600 font-medium">Training Modules</a>
                    <a href="#partners" class="text-gray-700 hover:text-blue-600 font-medium">Partners</a>
                    <a href="{{ route('login') }}" class="px-5 py-2 bg-gray-100 rounded-lg text-center font-medium">Sign In</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-center font-medium">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Parallax -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
            <div class="absolute top-20 left-10 w-72 h-72 blob opacity-70"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 blob opacity-70" style="animation-delay: 4s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 blob opacity-50" style="animation-delay: 2s;"></div>
        </div>
        
        <!-- Floating particles -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-4 h-4 bg-blue-400 rounded-full opacity-50 float-animation"></div>
            <div class="absolute top-3/4 right-1/3 w-3 h-3 bg-indigo-400 rounded-full opacity-50 float-animation" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-1/4 left-1/2 w-2 h-2 bg-purple-400 rounded-full opacity-50 float-animation" style="animation-delay: 2s;"></div>
        </div>
        
        <!-- Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div data-aos="fade-up" data-aos-duration="1000">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mb-6 pulse-glow-subtle">
                    <span class="w-2 h-2 bg-blue-600 rounded-full mr-2 animate-pulse"></span>
                    In collaboration with iTECH, HACU & ICT Division
                </span>
            </div>
            
            <h1 data-aos="fade-up" data-aos-delay="100" class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                Strengthening Healthcare
                <span class="text-gradient"> Workforce Capability</span>
            </h1>
            
            <p data-aos="fade-up" data-aos-delay="200" class="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Empowering healthcare professionals with digital learning resources for clinical applications, computer literacy, and continuous professional development.
            </p>
            
            <div data-aos="fade-up" data-aos-delay="300" class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold text-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 pulse-glow">
                    Access Training Portal
                </a>
                <a href="#courses" class="px-8 py-4 bg-white text-blue-600 rounded-full font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 hover-lift">
                    Explore Modules
                </a>
            </div>
            
            <!-- Stats -->
            <div data-aos="fade-up" data-aos-delay="400" class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center glass-blue rounded-2xl p-6 hover-lift">
                    <div class="text-3xl font-bold text-gradient">5000+</div>
                    <div class="text-gray-600">Healthcare Workers</div>
                </div>
                <div class="text-center glass-blue rounded-2xl p-6 hover-lift">
                    <div class="text-3xl font-bold text-gradient">150+</div>
                    <div class="text-gray-600">Training Modules</div>
                </div>
                <div class="text-center glass-blue rounded-2xl p-6 hover-lift">
                    <div class="text-3xl font-bold text-gradient">24/7</div>
                    <div class="text-gray-600">Access Available</div>
                </div>
                <div class="text-center glass-blue rounded-2xl p-6 hover-lift">
                    <div class="text-3xl font-bold text-gradient">100%</div>
                    <div class="text-gray-600">Digital Content</div>
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
    <section id="features" class="py-20 bg-white relative">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-blue-50 opacity-50"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold mb-4">
                    Transforming Healthcare <span class="text-gradient-blue">Training</span>
                </h2>
                <p data-aos="fade-up" data-aos-delay="100" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    From HIV information resources to comprehensive professional development - evolving to meet the health sector's training needs
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Card 1 -->
                <div data-aos="fade-up" data-aos-delay="100" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 card-hover">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mb-6 pulse-glow-subtle">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Standardized Training</h3>
                        <p class="text-gray-600">Ensure consistent, high-quality training content across all healthcare facilities and departments.</p>
                    </div>
                </div>
                
                <!-- Feature Card 2 -->
                <div data-aos="fade-up" data-aos-delay="200" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 card-hover">
                        <div class="w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center mb-6 pulse-glow-subtle">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Clinical Applications</h3>
                        <p class="text-gray-600">Access specialized training for clinical procedures, protocols, and best practices in healthcare delivery.</p>
                    </div>
                </div>
                
                <!-- Feature Card 3 -->
                <div data-aos="fade-up" data-aos-delay="300" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-sky-600 to-blue-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 card-hover">
                        <div class="w-16 h-16 bg-gradient-to-r from-sky-600 to-blue-600 rounded-xl flex items-center justify-center mb-6 pulse-glow-subtle">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Computer Literacy</h3>
                        <p class="text-gray-600">Build digital competencies essential for modern healthcare administration and patient management systems.</p>
                    </div>
                </div>
                
                <!-- Feature Card 4 -->
                <div data-aos="fade-up" data-aos-delay="400" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 card-hover">
                        <div class="w-16 h-16 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-xl flex items-center justify-center mb-6 pulse-glow-subtle">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Professional Development</h3>
                        <p class="text-gray-600">Comprehensive modules for doctors, nurses, and administrative staff to advance their careers.</p>
                    </div>
                </div>
                
                <!-- Feature Card 5 -->
                <div data-aos="fade-up" data-aos-delay="500" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-sky-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 card-hover">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-sky-600 rounded-xl flex items-center justify-center mb-6 pulse-glow-subtle">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Remote Access</h3>
                        <p class="text-gray-600">Reduce reliance on in-person training with 24/7 access to learning resources from any location.</p>
                    </div>
                </div>
                
                <!-- Feature Card 6 -->
                <div data-aos="fade-up" data-aos-delay="600" class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 card-hover">
                        <div class="w-16 h-16 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl flex items-center justify-center mb-6 pulse-glow-subtle">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Progress Tracking</h3>
                        <p class="text-gray-600">Monitor learning outcomes and ensure continuous improvement across the healthcare workforce.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Training Modules Section -->
    <section id="courses" class="py-20 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 relative">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold mb-4">
                    Comprehensive <span class="text-gradient-blue">Training Modules</span>
                </h2>
                <p data-aos="fade-up" data-aos-delay="100" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Specialized content developed for healthcare professionals at every level
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Module Card 1 -->
                <div data-aos="zoom-in" data-aos-delay="100" class="group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="relative h-48 bg-gradient-to-r from-red-500 to-pink-600 p-8 flex items-center justify-center overflow-hidden">
                            <div class="absolute inset-0 shimmer"></div>
                            <svg class="w-24 h-24 text-white opacity-50 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <div class="absolute top-4 right-4 bg-white text-red-600 px-3 py-1 rounded-full text-xs font-bold pulse-glow-subtle">
                                ESSENTIAL
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <span class="text-xs font-semibold text-red-600 bg-red-100 px-3 py-1 rounded-full">Clinical Training</span>
                                <span class="ml-auto text-sm text-gray-500">45 Modules</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">HIV Care & Treatment</h3>
                            <p class="text-gray-600 mb-4">Comprehensive HIV information resources, treatment protocols, and patient care guidelines.</p>
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-sm text-gray-600">For: All Healthcare Workers</div>
                                <span class="text-sm text-gray-500">20 hours</span>
                            </div>
                            <button class="w-full px-6 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                Start Learning
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Module Card 2 -->
                <div data-aos="zoom-in" data-aos-delay="200" class="group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="relative h-48 bg-gradient-to-r from-blue-500 to-indigo-600 p-8 flex items-center justify-center overflow-hidden">
                            <div class="absolute inset-0 shimmer"></div>
                            <svg class="w-24 h-24 text-white opacity-50 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                            <div class="absolute top-4 right-4 bg-white text-blue-600 px-3 py-1 rounded-full text-xs font-bold pulse-glow-subtle">
                                ADVANCED
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-3 py-1 rounded-full">Medical Procedures</span>
                                <span class="ml-auto text-sm text-gray-500">60 Modules</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Clinical Applications & Protocols</h3>
                            <p class="text-gray-600 mb-4">Advanced clinical procedures, diagnostic techniques, and treatment protocols for medical professionals.</p>
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-sm text-gray-600">For: Doctors & Nurses</div>
                                <span class="text-sm text-gray-500">40 hours</span>
                            </div>
                            <button class="w-full px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                Start Learning
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Module Card 3 -->
                <div data-aos="zoom-in" data-aos-delay="300" class="group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="relative h-48 bg-gradient-to-r from-indigo-500 to-purple-600 p-8 flex items-center justify-center overflow-hidden">
                            <div class="absolute inset-0 shimmer"></div>
                            <svg class="w-24 h-24 text-white opacity-50 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div class="absolute top-4 right-4 bg-white text-indigo-600 px-3 py-1 rounded-full text-xs font-bold pulse-glow-subtle">
                                FOUNDATIONAL
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <span class="text-xs font-semibold text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full">Digital Skills</span>
                                <span class="ml-auto text-sm text-gray-500">30 Modules</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Computer Literacy Program</h3>
                            <p class="text-gray-600 mb-4">Essential digital skills for healthcare information systems, EMR, and administrative tasks.</p>
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-sm text-gray-600">For: All Staff</div>
                                <span class="text-sm text-gray-500">15 hours</span>
                            </div>
                            <button class="w-full px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                Start Learning
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <a href="#" class="inline-flex items-center px-8 py-3 bg-white text-blue-600 rounded-full font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 pulse-glow-subtle">
                    Browse All Training Modules
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section id="partners" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold mb-4">
                    Our <span class="text-gradient-blue">Collaborative Partners</span>
                </h2>
                <p data-aos="fade-up" data-aos-delay="100" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Working together to deliver comprehensive healthcare training solutions
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Partner 1 -->
                <div data-aos="fade-up" data-aos-delay="100" class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl text-center hover-lift card-hover">
                    <div class="w-24 h-24 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full mx-auto mb-4 flex items-center justify-center pulse-glow-subtle">
                        <span class="text-white text-2xl font-bold">iTECH</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">iTECH</h3>
                    <p class="text-gray-600">External technology partner providing LMS infrastructure and technical expertise</p>
                </div>
                
                <!-- Partner 2 -->
                <div data-aos="fade-up" data-aos-delay="200" class="bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-2xl text-center hover-lift card-hover">
                    <div class="w-24 h-24 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center pulse-glow-subtle">
                        <span class="text-white text-2xl font-bold">HACU</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">HACU</h3>
                    <p class="text-gray-600">Healthcare Assessment and Capacity Unit supporting training content development</p>
                </div>
                
                <!-- Partner 3 -->
                <div data-aos="fade-up" data-aos-delay="300" class="bg-gradient-to-br from-purple-50 to-blue-50 p-8 rounded-2xl text-center hover-lift card-hover">
                    <div class="w-24 h-24 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center pulse-glow-subtle">
                        <span class="text-white text-2xl font-bold">ICT</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">ICT Division</h3>
                    <p class="text-gray-600">Ministry of Health ICT team ensuring seamless system integration</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('images/moh_logo.jpg') }}" alt="Ministry of Health" alt="MOH Logo" class="h-10 w-10 rounded-full">
                        <span class="text-xl font-bold">Ministry of Health</span>
                    </div>
                    <p class="text-gray-400 mb-4">Strengthening healthcare workforce capability through innovative digital learning.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About LMS</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Training Modules</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Resources</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQs</a></li>
                    </ul>
                </div>
                
                <!-- Support -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Technical Support</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">User Guide</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact IT</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Information</h4>
                    <p class="text-gray-400 mb-2">Ministry of Health</p>
                    <p class="text-gray-400 mb-2">Learning & Development Unit</p>
                    <p class="text-gray-400 mb-2">Email: lms@health.gov.tt</p>
                    <p class="text-gray-400">Phone: 1-868-XXX-XXXX</p>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2024 Ministry of Health, Trinidad and Tobago. All rights reserved. | In collaboration with iTECH, HACU & ICT Division</p>
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