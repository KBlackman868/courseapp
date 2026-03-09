import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        department: '',
        organization: '',
        phone: '',
        date_of_birth: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    // Calculate max date (18 years ago)
    const maxDate = new Date();
    maxDate.setFullYear(maxDate.getFullYear() - 18);
    const maxDateStr = maxDate.toISOString().split('T')[0];

    return (
        <GuestLayout>
            <Head title="Register" />

            <form onSubmit={submit}>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel htmlFor="first_name" value="First Name" />
                        <TextInput
                            id="first_name"
                            name="first_name"
                            value={data.first_name}
                            className="mt-1 block w-full"
                            autoComplete="given-name"
                            isFocused={true}
                            onChange={(e) => setData('first_name', e.target.value)}
                            required
                        />
                        <InputError message={errors.first_name} className="mt-2" />
                    </div>

                    <div>
                        <InputLabel htmlFor="last_name" value="Last Name" />
                        <TextInput
                            id="last_name"
                            name="last_name"
                            value={data.last_name}
                            className="mt-1 block w-full"
                            autoComplete="family-name"
                            onChange={(e) => setData('last_name', e.target.value)}
                            required
                        />
                        <InputError message={errors.last_name} className="mt-2" />
                    </div>
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        onChange={(e) => setData('email', e.target.value)}
                        required
                    />
                    <InputError message={errors.email} className="mt-2" />
                    <p className="mt-1 text-xs text-gray-500">
                        MOH staff: use your @health.gov.tt email. External users: use any email.
                    </p>
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="department" value="Department" />
                    <TextInput
                        id="department"
                        name="department"
                        value={data.department}
                        className="mt-1 block w-full"
                        onChange={(e) => setData('department', e.target.value)}
                        required
                    />
                    <InputError message={errors.department} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="organization" value="Organization (optional)" />
                    <TextInput
                        id="organization"
                        name="organization"
                        value={data.organization}
                        className="mt-1 block w-full"
                        onChange={(e) => setData('organization', e.target.value)}
                    />
                    <InputError message={errors.organization} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="date_of_birth" value="Date of Birth" />
                    <TextInput
                        id="date_of_birth"
                        type="date"
                        name="date_of_birth"
                        value={data.date_of_birth}
                        className="mt-1 block w-full"
                        max={maxDateStr}
                        onChange={(e) => setData('date_of_birth', e.target.value)}
                        required
                    />
                    <InputError message={errors.date_of_birth} className="mt-2" />
                    <p className="mt-1 text-xs text-gray-500">You must be at least 18 years old.</p>
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) => setData('password', e.target.value)}
                        required
                    />
                    <InputError message={errors.password} className="mt-2" />
                    <p className="mt-1 text-xs text-gray-500">
                        MOH staff: 14 characters min. External users: 12 characters min.
                    </p>
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password_confirmation" value="Confirm Password" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        value={data.password_confirmation}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        required
                    />
                    <InputError message={errors.password_confirmation} className="mt-2" />
                </div>

                <div className="mt-4 flex items-center justify-between">
                    <div className="text-sm">
                        <Link
                            href={route('login')}
                            className="text-gray-600 underline hover:text-gray-900"
                        >
                            Already registered?
                        </Link>
                    </div>

                    <PrimaryButton className="ms-4" disabled={processing}>
                        Register
                    </PrimaryButton>
                </div>

                <div className="mt-4 text-center">
                    <Link
                        href={route('register.external')}
                        className="text-sm text-indigo-600 hover:text-indigo-500"
                    >
                        External user? Register here instead
                    </Link>
                </div>
            </form>
        </GuestLayout>
    );
}
