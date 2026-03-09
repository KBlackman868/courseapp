import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import PasswordChecklist, { usePasswordValidation } from '@/Components/PasswordChecklist';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

const departments = [
    'Administration',
    'Chronic Disease',
    'Community Health',
    'County Medical Office of Health',
    'Dental',
    'Environmental Health',
    'Epidemiology',
    'Health Education',
    'Health Policy',
    'Human Resources',
    'Information Technology',
    'Insect Vector Control',
    'Legal',
    'Medical Stores',
    'Mental Health',
    'Nursing',
    'Nutrition',
    'Occupational Health',
    'Pharmacy',
    'Planning',
    'Population Programme',
    'Primary Care',
    'Procurement',
    'Public Health',
    'Public Health Laboratory',
    'Quality Standards',
    'Veterinary Public Health',
    'Other',
];

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        department: '',
        password: '',
        password_confirmation: '',
        terms: false,
    });

    const { allValid } = usePasswordValidation(
        data.password,
        data.first_name,
        data.last_name,
        data.email
    );

    const submit = (e) => {
        e.preventDefault();
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

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
                    <select
                        id="department"
                        name="department"
                        value={data.department}
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        onChange={(e) => setData('department', e.target.value)}
                        required
                    >
                        <option value="">Select a department</option>
                        {departments.map((dept) => (
                            <option key={dept} value={dept}>
                                {dept}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.department} className="mt-2" />
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
                    <PasswordChecklist
                        password={data.password}
                        firstName={data.first_name}
                        lastName={data.last_name}
                        email={data.email}
                    />
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

                <div className="mt-4">
                    <label className="flex items-start">
                        <input
                            type="checkbox"
                            name="terms"
                            checked={data.terms}
                            onChange={(e) => setData('terms', e.target.checked)}
                            className="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        />
                        <span className="ms-2 text-sm text-gray-600">
                            I agree to the{' '}
                            <a
                                href={route('terms')}
                                target="_blank"
                                className="text-indigo-600 underline hover:text-indigo-500"
                            >
                                Terms and Conditions
                            </a>{' '}
                            and{' '}
                            <a
                                href={route('privacy-policy')}
                                target="_blank"
                                className="text-indigo-600 underline hover:text-indigo-500"
                            >
                                Privacy Policy
                            </a>
                        </span>
                    </label>
                    <InputError message={errors.terms} className="mt-2" />
                </div>

                <div className="mt-6 flex items-center justify-between">
                    <Link
                        href={route('login')}
                        className="text-sm text-gray-600 underline hover:text-gray-900"
                    >
                        Already registered?
                    </Link>

                    <PrimaryButton
                        className="ms-4"
                        disabled={processing || !allValid || !data.terms}
                    >
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
