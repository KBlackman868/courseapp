import { Head } from '@inertiajs/react';

export default function Show({ user, enrollments }) {
    const avatarSrc = user.profile_photo_url
        || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.first_name + ' ' + user.last_name)}&background=6366f1&color=fff`;

    return (
        <>
            <Head title="Profile" />

            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                {/* Avatar + Basic Info */}
                <div className="bg-white p-6 rounded-lg shadow">
                    <img
                        src={avatarSrc}
                        className="w-32 h-32 rounded-full mx-auto object-cover"
                        alt="Avatar"
                    />
                    <h2 className="mt-4 text-center text-2xl font-semibold text-gray-900">
                        {user.first_name} {user.last_name}
                    </h2>
                    <p className="text-center text-gray-600">{user.email}</p>
                </div>

                {/* Enrolled Courses */}
                <div className="md:col-span-2 bg-white p-6 rounded-lg shadow">
                    <h3 className="text-xl font-bold mb-4 text-gray-800">My Courses</h3>
                    {enrollments.length === 0 ? (
                        <p className="text-gray-600">You have no approved courses yet.</p>
                    ) : (
                        <ul className="space-y-2">
                            {enrollments.map((enroll) => (
                                <li
                                    key={enroll.id}
                                    className="flex justify-between items-center p-3 bg-gray-50 rounded"
                                >
                                    <span className="font-medium text-gray-900">
                                        {enroll.course.title}
                                    </span>
                                    <span className="text-sm text-green-600 uppercase">
                                        {enroll.status}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    )}
                </div>
            </div>
        </>
    );
}
