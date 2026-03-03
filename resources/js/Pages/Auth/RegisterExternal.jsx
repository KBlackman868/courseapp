import { Head, Link, useForm } from '@inertiajs/react';

export default function RegisterExternal() {
    const form = useForm({
        first_name: '',
        last_name: '',
        email: '',
        organization: '',
        date_of_birth: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        form.post('/register/external');
    };

    return (
        <>
            <Head title="External Registration" />
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 to-purple-50 py-12">
                <div className="w-full max-w-lg bg-white rounded-xl shadow-lg p-8">
                    <div className="text-center mb-6">
                        <h2 className="text-2xl font-bold text-gray-900">External User Registration</h2>
                        <p className="text-gray-600 mt-1">Create your account to access courses</p>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" value={form.data.first_name} onChange={(e) => form.setData('first_name', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                {form.errors.first_name && <p className="mt-1 text-sm text-red-600">{form.errors.first_name}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" value={form.data.last_name} onChange={(e) => form.setData('last_name', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                {form.errors.last_name && <p className="mt-1 text-sm text-red-600">{form.errors.last_name}</p>}
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            {form.errors.email && <p className="mt-1 text-sm text-red-600">{form.errors.email}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Organization</label>
                            <input type="text" value={form.data.organization} onChange={(e) => form.setData('organization', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            {form.errors.organization && <p className="mt-1 text-sm text-red-600">{form.errors.organization}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input type="date" value={form.data.date_of_birth} onChange={(e) => form.setData('date_of_birth', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            {form.errors.date_of_birth && <p className="mt-1 text-sm text-red-600">{form.errors.date_of_birth}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Password (min 12 characters)</label>
                            <input type="password" value={form.data.password} onChange={(e) => form.setData('password', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            {form.errors.password && <p className="mt-1 text-sm text-red-600">{form.errors.password}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" value={form.data.password_confirmation} onChange={(e) => form.setData('password_confirmation', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        </div>

                        <button type="submit" disabled={form.processing} className="w-full rounded-md bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50">
                            {form.processing ? 'Registering...' : 'Create Account'}
                        </button>
                    </form>

                    <p className="mt-4 text-center text-sm text-gray-600">
                        Already have an account? <Link href="/login" className="text-indigo-600 hover:text-indigo-500">Sign in</Link>
                    </p>
                </div>
            </div>
        </>
    );
}
