import '../css/app.css';
import './bootstrap';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import DashboardLayout from '@/Layouts/DashboardLayout';
import GuestLayout from '@/Layouts/GuestLayout';

const appName = import.meta.env.VITE_APP_NAME || 'Learn About Health';

const guestPages = ['Auth/Login', 'Auth/Register', 'Auth/ForgotPassword', 'Auth/ResetPassword', 'Auth/VerifyEmail', 'Auth/ConfirmPassword', 'Auth/RegisterExternal', 'Auth/OtpVerify', 'Auth/MohRequestAccount', 'Auth/MohRequestSubmitted', 'Auth/VerifyEmailOtp'];
const noLayoutPages = ['Welcome', 'Dashboard/AccountPending', 'Policies/Terms', 'Policies/Privacy', 'Legal/Terms', 'Legal/Privacy', 'Help/Faq', 'Error'];

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        );
        return page.then((module) => {
            const component = module.default;
            if (!component.layout) {
                if (noLayoutPages.includes(name)) {
                    // No layout
                } else if (guestPages.includes(name)) {
                    component.layout = (page) => <GuestLayout>{page}</GuestLayout>;
                } else {
                    component.layout = (page) => <DashboardLayout>{page}</DashboardLayout>;
                }
            }
            return module;
        });
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
