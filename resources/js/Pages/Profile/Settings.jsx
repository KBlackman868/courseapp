import { Head, useForm, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import DashboardLayout from '@/Layouts/DashboardLayout';

export default function Settings({ user }) {
    const { auth } = usePage().props;
    const isAdmin = auth.user?.roles?.some(r =>
        ['admin', 'superadmin', 'course_admin'].includes(r.name)
    );
    const Layout = isAdmin ? AdminLayout : DashboardLayout;

    const photoForm = useForm({ profile_photo: null });
    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const handlePhotoSubmit = (e) => {
        e.preventDefault();
        photoForm.post(route('profile.photo'), {
            forceFormData: true,
        });
    };

    const handlePasswordSubmit = (e) => {
        e.preventDefault();
        passwordForm.post(route('profile.password'), {
            onSuccess: () => passwordForm.reset(),
        });
    };

    return (
        <Layout
            title="Settings"
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Settings
                </h2>
            }
        >
            <Head title="Settings" />

            <div className="space-y-8">
                {/* Change Profile Photo */}
                <div className="bg-white p-6 rounded-lg shadow">
                    <h3 className="text-lg font-semibold text-gray-800 mb-4">
                        Update Profile Picture
                    </h3>
                    <form onSubmit={handlePhotoSubmit} className="space-y-4">
                        <input
                            type="file"
                            accept="image/*"
                            onChange={(e) =>
                                photoForm.setData('profile_photo', e.target.files[0])
                            }
                            className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                        {photoForm.errors.profile_photo && (
                            <p className="text-sm text-red-600">{photoForm.errors.profile_photo}</p>
                        )}
                        <button
                            type="submit"
                            disabled={photoForm.processing || !photoForm.data.profile_photo}
                            className="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50"
                        >
                            Upload
                        </button>
                    </form>
                </div>

                {/* Change Password */}
                <div className="bg-white p-6 rounded-lg shadow">
                    <h3 className="text-lg font-semibold text-gray-800 mb-4">
                        Change Password
                    </h3>
                    <form onSubmit={handlePasswordSubmit} className="space-y-4 max-w-md">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Current Password
                            </label>
                            <input
                                type="password"
                                value={passwordForm.data.current_password}
                                onChange={(e) =>
                                    passwordForm.setData('current_password', e.target.value)
                                }
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            {passwordForm.errors.current_password && (
                                <p className="mt-1 text-sm text-red-600">
                                    {passwordForm.errors.current_password}
                                </p>
                            )}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                New Password
                            </label>
                            <input
                                type="password"
                                value={passwordForm.data.password}
                                onChange={(e) =>
                                    passwordForm.setData('password', e.target.value)
                                }
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            {passwordForm.errors.password && (
                                <p className="mt-1 text-sm text-red-600">
                                    {passwordForm.errors.password}
                                </p>
                            )}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Confirm New Password
                            </label>
                            <input
                                type="password"
                                value={passwordForm.data.password_confirmation}
                                onChange={(e) =>
                                    passwordForm.setData('password_confirmation', e.target.value)
                                }
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>
                        <button
                            type="submit"
                            disabled={passwordForm.processing}
                            className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
                        >
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </Layout>
    );
}
