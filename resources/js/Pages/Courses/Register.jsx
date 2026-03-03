import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function Register({ course }) {
    const { flash } = usePage().props;
    const [processing, setProcessing] = useState(false);

    const handleEnroll = () => {
        setProcessing(true);
        router.post(`/courses/${course.id}/enroll`, {}, {
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <>
            <Head title={`Register - ${course.title}`} />

            <div className="space-y-8">
                {/* Flash Messages */}
                {flash?.success && (
                    <div className="rounded-md bg-green-50 p-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg className="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-green-800">{flash.success}</p>
                            </div>
                        </div>
                    </div>
                )}
                {flash?.error && (
                    <div className="rounded-md bg-red-50 p-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg className="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-red-800">{flash.error}</p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Hero Section */}
                <div className="overflow-hidden rounded-lg shadow">
                    {course.image ? (
                        <img
                            src={course.image}
                            alt={course.title}
                            className="h-64 w-full object-cover"
                        />
                    ) : (
                        <div className="flex h-64 items-center justify-center bg-gradient-to-r from-indigo-600 to-purple-600">
                            <svg className="h-20 w-20 text-white/50" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                                <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                            </svg>
                        </div>
                    )}
                </div>

                {/* Course Title and Description */}
                <div className="rounded-lg bg-white p-6 shadow">
                    <h1 className="text-3xl font-bold text-gray-900">{course.title}</h1>
                    {course.description && (
                        <div className="mt-4 text-gray-600 leading-relaxed whitespace-pre-line">
                            {course.description}
                        </div>
                    )}
                </div>

                {/* What You Will Learn */}
                <div className="rounded-lg bg-white p-6 shadow">
                    <h2 className="text-xl font-bold text-gray-900 mb-4">What You Will Learn</h2>
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {course.learning_outcomes && course.learning_outcomes.length > 0 ? (
                            course.learning_outcomes.map((outcome, index) => (
                                <div key={index} className="flex items-start gap-3">
                                    <div className="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <svg className="h-4 w-4 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                                        </svg>
                                    </div>
                                    <span className="text-sm text-gray-700">{outcome}</span>
                                </div>
                            ))
                        ) : (
                            <>
                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span className="text-indigo-600 text-sm font-bold">1</span>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Comprehensive Curriculum</h3>
                                        <p className="text-sm text-gray-600">Access structured learning materials designed by experts.</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span className="text-indigo-600 text-sm font-bold">2</span>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Interactive Content</h3>
                                        <p className="text-sm text-gray-600">Engage with quizzes, videos, and hands-on exercises.</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span className="text-indigo-600 text-sm font-bold">3</span>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Self-Paced Learning</h3>
                                        <p className="text-sm text-gray-600">Complete the course at your own pace and convenience.</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span className="text-indigo-600 text-sm font-bold">4</span>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Certificate on Completion</h3>
                                        <p className="text-sm text-gray-600">Earn a certificate upon successfully completing the course.</p>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </div>

                {/* Enroll Section */}
                <div className="rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-center shadow-lg">
                    <h2 className="text-2xl font-bold text-white">Ready to Get Started?</h2>
                    <p className="mt-2 text-indigo-100">
                        Join other learners and start your learning journey today.
                    </p>
                    <button
                        onClick={handleEnroll}
                        disabled={processing}
                        className="mt-6 inline-flex items-center rounded-md bg-white px-8 py-3 text-base font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        {processing ? (
                            <>
                                <svg className="animate-spin -ml-1 mr-2 h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enrolling...
                            </>
                        ) : (
                            'Enroll Now'
                        )}
                    </button>
                </div>

                {/* Back Link */}
                <div className="text-center">
                    <Link
                        href="/catalog"
                        className="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        <svg className="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fillRule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clipRule="evenodd" />
                        </svg>
                        Back to Course Catalog
                    </Link>
                </div>
            </div>
        </>
    );
}
