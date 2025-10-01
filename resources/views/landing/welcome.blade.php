<x-layouts>
    <x-slot:heading>
        Welcome to MOH Learning Portal
    </x-slot:heading>

    <!-- Hero Section -->
    <div class="-mx-6 -mt-8 md:-mx-8">
        <section class="relative min-h-[600px] flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-t-2xl">
            <!-- Animated Background -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-72 h-72 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 4s;"></div>
            </div>
            
            <!-- Hero Content -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-20">
                <div>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mb-6">
                        <span class="w-2 h-2 bg-blue-600 rounded-full mr-2 animate-pulse"></span>
                        In collaboration with iTECH, HACU & ICT Division
                    </span>
                </div>
                
                <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                    Strengthening Healthcare
                    <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent"> Workforce Capability</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Empowering healthcare professionals with digital learning resources for clinical applications, computer literacy, and continuous professional development.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                    @guest
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold text-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                            Access Training Portal
                        </a>
                        <a href="#courses" class="px-8 py-4 bg-white text-blue-600 rounded-full font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            Explore Modules
                        </a>
                    @else
                        @if(auth()->user()->hasRole(['admin', 'superadmin']))
                            <a href="{{ route('courses.index') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold text-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                                Manage Training Modules
                            </a>
                            <a href="{{ route('courses.create') }}" class="px-8 py-4 bg-white text-blue-600 rounded-full font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                                Create New Course
                            </a>
                        @else
                            <a href="{{ route('courses.index') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold text-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                                Browse Training Portal
                            </a>
                            <a href="{{ route('mycourses') }}" class="px-8 py-4 bg-white text-blue-600 rounded-full font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                                My Enrolled Courses
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center bg-white/80 backdrop-blur rounded-2xl p-6">
                        <div class="text-3xl font-bold text-blue-600">5000+</div>
                        <div class="text-gray-600">Healthcare Workers</div>
                    </div>
                    <div class="text-center bg-white/80 backdrop-blur rounded-2xl p-6">
                        <div class="text-3xl font-bold text-blue-600">150+</div>
                        <div class="text-gray-600">Training Modules</div>
                    </div>
                    <div class="text-center bg-white/80 backdrop-blur rounded-2xl p-6">
                        <div class="text-3xl font-bold text-blue-600">24/7</div>
                        <div class="text-gray-600">Online Access</div>
                    </div>
                    <div class="text-center bg-white/80 backdrop-blur rounded-2xl p-6">
                        <div class="text-3xl font-bold text-blue-600">100%</div>
                        <div class="text-gray-600">Digital Learning</div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Transforming Healthcare <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Training</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    From HIV information resources to comprehensive professional development - evolving to meet the health sector's training needs
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Cards -->
                <div class="group relative">
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Standardized Training</h3>
                        <p class="text-gray-600">Ensure consistent, high-quality training content across all healthcare facilities and departments.</p>
                    </div>
                </div>
                
                <div class="group relative">
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                        <div class="w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Clinical Applications</h3>
                        <p class="text-gray-600">Access specialized training for clinical procedures, protocols, and best practices in healthcare delivery.</p>
                    </div>
                </div>
                
                <div class="group relative">
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                        <div class="w-16 h-16 bg-gradient-to-r from-sky-600 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Computer Literacy</h3>
                        <p class="text-gray-600">Build digital competencies essential for modern healthcare administration and patient management systems.</p>
                    </div>
                </div>
                
                <div class="group relative">
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                        <div class="w-16 h-16 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Professional Development</h3>
                        <p class="text-gray-600">Comprehensive modules for doctors, nurses, and administrative staff to advance their careers.</p>
                    </div>
                </div>
                
                <div class="group relative">
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-sky-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Remote Access</h3>
                        <p class="text-gray-600">Reduce reliance on in-person training with 24/7 access to learning resources from any location.</p>
                    </div>
                </div>
                
                <div class="group relative">
                    <div class="relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                        <div class="w-16 h-16 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl flex items-center justify-center mb-6">
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
    <section id="courses" class="py-20 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Comprehensive <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Training Modules</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Specialized content developed for healthcare professionals at every level
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Module Cards -->
                <div class="group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="relative h-48 bg-gradient-to-r from-red-500 to-pink-600 p-8 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <div class="absolute top-4 right-4 bg-white text-red-600 px-3 py-1 rounded-full text-xs font-bold">
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
                            @auth
                                <a href="{{ route('courses.index') }}" class="block w-full px-6 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center">
                                    Start Learning
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="block w-full px-6 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center">
                                    Get Started
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                
                <div class="group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="relative h-48 bg-gradient-to-r from-blue-500 to-indigo-600 p-8 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                            <div class="absolute top-4 right-4 bg-white text-blue-600 px-3 py-1 rounded-full text-xs font-bold">
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
                            @auth
                                <a href="{{ route('courses.index') }}" class="block w-full px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center">
                                    Start Learning
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="block w-full px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center">
                                    Get Started
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                
                <div class="group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="relative h-48 bg-gradient-to-r from-indigo-500 to-purple-600 p-8 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div class="absolute top-4 right-4 bg-white text-indigo-600 px-3 py-1 rounded-full text-xs font-bold">
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
                            @auth
                                <a href="{{ route('courses.index') }}" class="block w-full px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center">
                                    Start Learning
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="block w-full px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center">
                                    Get Started
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('courses.index') }}" class="inline-flex items-center px-8 py-3 bg-white text-blue-600 rounded-full font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
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
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Our <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Collaborative Partners</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Working together to deliver comprehensive healthcare training solutions
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Partner Cards -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl text-center hover:shadow-lg transition-shadow">
                    <div class="w-24 h-24 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">iTECH</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">iTECH</h3>
                    <p class="text-gray-600">External technology partner providing LMS infrastructure and technical expertise</p>
                </div>
                
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-2xl text-center hover:shadow-lg transition-shadow">
                    <div class="w-24 h-24 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">HACU</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">HACU</h3>
                    <p class="text-gray-600">Healthcare Assessment and Capacity Unit supporting training content development</p>
                </div>
                
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 p-8 rounded-2xl text-center hover:shadow-lg transition-shadow">
                    <div class="w-24 h-24 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">ICT</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">ICT Division</h3>
                    <p class="text-gray-600">Ministry of Health ICT team ensuring seamless system integration</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gradient-to-br from-gray-900 to-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Get in Touch</h2>
                <p class="text-xl text-gray-300">Have questions about the Learning Management System?</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Email</h3>
                    <p class="text-gray-300">lms@health.gov.tt</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Phone</h3>
                    <p class="text-gray-300">1-868-XXX-XXXX</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Location</h3>
                    <p class="text-gray-300">Ministry of Health<br>Trinidad and Tobago</p>
                </div>
            </div>
        </div>
    </section>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</x-layouts>