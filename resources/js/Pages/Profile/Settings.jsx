import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Settings({ user }) {
    const [confirmingDeletion, setConfirmingDeletion] = useState(false);

    const photoForm = useForm({ profile_photo: null });
    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });
    const deleteForm = useForm({ password: '' });

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

    const handleDeleteAccount = (e) => {
        e.preventDefault();
        deleteForm.delete(route('profile.destroy'), {
            onSuccess: () => setConfirmingDeletion(false),
            onError: () => {},
        });
    };

    return (
        <>
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

                {/* Delete Account */}
                <div className="bg-white p-6 rounded-lg shadow border border-red-200">
                    <h3 className="text-lg font-semibold text-red-600 mb-2">
                        Delete Account
                    </h3>
                    <p className="text-sm text-gray-600 mb-4">
                        Once your account is deleted, all of its resources and data will be
                        permanently deleted. Please enter your password to confirm you would
                        like to permanently delete your account.
                    </p>

                    {!confirmingDeletion ? (
                        <button
                            onClick={() => setConfirmingDeletion(true)}
                            className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                        >
                            Delete Account
                        </button>
                    ) : (
                        <form onSubmit={handleDeleteAccount} className="space-y-4 max-w-md">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Confirm your password
                                </label>
                                <input
                                    type="password"
                                    value={deleteForm.data.password}
                                    onChange={(e) =>
                                        deleteForm.setData('password', e.target.value)
                                    }
                                    placeholder="Enter your password to confirm"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                />
                                {deleteForm.errors.password && (
                                    <p className="mt-1 text-sm text-red-600">
                                        {deleteForm.errors.password}
                                    </p>
                                )}
                            </div>
                            <div className="flex gap-3">
                                <button
                                    type="submit"
                                    disabled={deleteForm.processing}
                                    className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                                >
                                    Permanently Delete Account
                                </button>
                                <button
                                    type="button"
                                    onClick={() => {
                                        setConfirmingDeletion(false);
                                        deleteForm.reset();
                                    }}
                                    className="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    )}
                </div>
            </div>
        </>
    );
}
