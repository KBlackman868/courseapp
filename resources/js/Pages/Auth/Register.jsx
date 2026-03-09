import { useState, useMemo } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

const css = `
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes float1 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -40px) scale(1.05); }
    66% { transform: translate(-20px, 20px) scale(0.95); }
}
@keyframes float2 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(-25px, 35px) scale(1.08); }
    66% { transform: translate(15px, -25px) scale(0.92); }
}
@keyframes float3 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(20px, 25px) scale(0.96); }
    66% { transform: translate(-30px, -15px) scale(1.04); }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes pulse {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 0.8; }
}
.animate-gradient { background-size: 200% 200%; animation: gradientShift 8s ease infinite; }
.animate-float1 { animation: float1 7s ease-in-out infinite; }
.animate-float2 { animation: float2 9s ease-in-out infinite; }
.animate-float3 { animation: float3 11s ease-in-out infinite; }
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
.animate-fade-in-up-delay-1 { animation: fadeInUp 0.6s ease-out 0.1s forwards; opacity: 0; }
.animate-fade-in-up-delay-2 { animation: fadeInUp 0.6s ease-out 0.2s forwards; opacity: 0; }
.animate-fade-in-up-delay-3 { animation: fadeInUp 0.6s ease-out 0.3s forwards; opacity: 0; }
.animate-pulse-slow { animation: pulse 3s ease-in-out infinite; }
.input-focus-glow:focus { box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15); border-color: #4F46E5; }
.btn-hover:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4); }
.btn-hover:active { transform: translateY(0); }
`;

function getPasswordStrength(password) {
    if (!password) return { score: 0, label: '', color: '' };
    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;

    if (score <= 1) return { score: 1, label: 'Weak', color: 'bg-red-500' };
    if (score <= 2) return { score: 2, label: 'Fair', color: 'bg-orange-500' };
    if (score <= 3) return { score: 3, label: 'Good', color: 'bg-yellow-500' };
    if (score <= 4) return { score: 4, label: 'Strong', color: 'bg-green-500' };
    return { score: 5, label: 'Very Strong', color: 'bg-emerald-500' };
}

function calculateAge(dateString) {
    if (!dateString) return null;
    const birth = new Date(dateString);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms_accepted: false,
    });
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);
    const [ageError, setAgeError] = useState('');
    const [termsError, setTermsError] = useState('');

    const strength = useMemo(() => getPasswordStrength(data.password), [data.password]);

    const maxDate = useMemo(() => {
        const d = new Date();
        d.setFullYear(d.getFullYear() - 18);
        return d.toISOString().split('T')[0];
    }, []);

    const submit = (e) => {
        e.preventDefault();
        setAgeError('');
        setTermsError('');

        const age = calculateAge(data.date_of_birth);
        if (age === null || age < 18) {
            setAgeError('You must be 18 or older to register.');
            return;
        }

        if (!data.terms_accepted) {
            setTermsError('You must accept the Terms and Conditions to register.');
            return;
        }

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    // Calculate max date (18 years ago)
    const maxDate = new Date();
    maxDate.setFullYear(maxDate.getFullYear() - 18);
    const maxDateStr = maxDate.toISOString().split('T')[0];

    return (
        <>
            <Head title="Register" />

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="name" value="Name" />

                    <TextInput
                        id="name"
                        name="name"
                        value={data.name}
                        className="mt-1 block w-full"
                        autoComplete="name"
                        isFocused={true}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                    />

                    <InputError message={errors.name} className="mt-2" />
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
                </div>

                <div className="mt-4">
                    <InputLabel
                        htmlFor="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        value={data.password_confirmation}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) =>
                            setData('password_confirmation', e.target.value)
                        }
                        required
                    />

                    <InputError
                        message={errors.password_confirmation}
                        className="mt-2"
                    />
                </div>

                <div className="mt-4 flex items-center justify-end">
                    <Link
                        href={route('login')}
                        className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Already registered?
                    </Link>

                    <PrimaryButton className="ms-4" disabled={processing}>
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
