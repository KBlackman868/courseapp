import { useMemo } from 'react';

/**
 * Shared password requirements checklist component.
 * Used on Register, Reset Password, and Change Password forms.
 *
 * @param {string} password - Current password value
 * @param {string} firstName - User's first name (for name-check)
 * @param {string} lastName - User's last name (for name-check)
 * @param {string} email - User's email (for email-check)
 */
export function usePasswordValidation(password, firstName = '', lastName = '', email = '') {
    return useMemo(() => {
        const checks = {
            minLength: password.length >= 12,
            hasUpper: /[A-Z]/.test(password),
            hasLower: /[a-z]/.test(password),
            hasDigit: /[0-9]/.test(password),
            hasSpecial: /[!@#$%^&*()\-_=+\[\]{}|;:'",.?/]/.test(password),
            noForbidden: !/[\\~<>]/.test(password),
            noName: true,
        };

        // Check name parts (3+ char substrings)
        if (password.length > 0 && (firstName || lastName)) {
            const lowerPw = password.toLowerCase();
            const parts = [
                ...splitIntoParts(firstName.toLowerCase()),
                ...splitIntoParts(lastName.toLowerCase()),
            ];
            for (const part of parts) {
                if (part.length >= 3 && lowerPw.includes(part)) {
                    checks.noName = false;
                    break;
                }
            }
            // Also check email username
            if (email) {
                const emailUser = email.split('@')[0].toLowerCase();
                if (emailUser.length >= 3 && lowerPw.includes(emailUser)) {
                    checks.noName = false;
                }
            }
        }

        const allValid = Object.values(checks).every(Boolean);

        return { checks, allValid };
    }, [password, firstName, lastName, email]);
}

function splitIntoParts(str) {
    if (!str || str.length < 3) return str ? [str] : [];
    const parts = [];
    for (let i = 0; i <= str.length - 3; i++) {
        parts.push(str.substring(i, i + 3));
    }
    return parts;
}

const requirements = [
    { key: 'minLength', label: 'At least 12 characters' },
    { key: 'hasUpper', label: 'At least one uppercase letter (A-Z)' },
    { key: 'hasLower', label: 'At least one lowercase letter (a-z)' },
    { key: 'hasDigit', label: 'At least one number (0-9)' },
    { key: 'hasSpecial', label: 'At least one special character (! @ # $ % ^ & *)' },
    { key: 'noForbidden', label: 'Does not contain \\ ~ < >' },
    { key: 'noName', label: 'Does not contain parts of your name or email' },
];

export default function PasswordChecklist({ password, firstName, lastName, email }) {
    const { checks } = usePasswordValidation(password, firstName, lastName, email);
    const started = password.length > 0;

    if (!started) return null;

    return (
        <div className="mt-2 rounded-lg border border-gray-200 bg-gray-50 p-3">
            <p className="mb-2 text-xs font-semibold text-gray-600 uppercase tracking-wide">
                Password Requirements
            </p>
            <ul className="space-y-1">
                {requirements.map(({ key, label }) => {
                    const met = checks[key];
                    return (
                        <li key={key} className="flex items-center text-sm">
                            <span className={`mr-2 flex-shrink-0 ${met ? 'text-green-600' : 'text-red-500'}`}>
                                {met ? '\u2705' : '\u274C'}
                            </span>
                            <span className={met ? 'text-green-700' : 'text-red-600'}>
                                {label}
                                {key === 'minLength' && !met && (
                                    <span className="text-gray-500"> ({password.length}/12)</span>
                                )}
                            </span>
                        </li>
                    );
                })}
            </ul>
        </div>
    );
}
