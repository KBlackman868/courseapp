import { Head, usePage } from '@inertiajs/react';

export default function MoodleEditor() {
    const { auth } = usePage().props;
    const user = auth?.user;

    return (
        <>
            <Head title="Moodle Editor Dashboard" />

            <div className="flex items-center justify-center" style={{ minHeight: 'calc(100vh - 16rem)' }}>
                <div className="w-full max-w-lg overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 shadow-xl">
                    <div className="px-8 py-10 text-center sm:px-12 sm:py-14">
                        <h1 className="text-2xl font-bold text-white sm:text-3xl">
                            Welcome, {user?.first_name}
                        </h1>
                        <p className="mx-auto mt-3 max-w-sm text-base text-indigo-100">
                            You are signed in as a <span className="font-semibold text-white">Moodle Editor</span>.
                            Click below to open Moodle and manage courses.
                        </p>

                        <div className="mt-8">
                            <a
                                href="/moodle/sso"
                                className="inline-flex items-center gap-3 rounded-xl bg-white px-8 py-4 text-lg font-bold text-indigo-700 shadow-lg transition-all duration-200 hover:bg-indigo-50 hover:shadow-xl hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-white/50"
                            >
                                <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                                Open Moodle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
