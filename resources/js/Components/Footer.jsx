export default function Footer() {
    return (
        <footer className="border-t border-gray-200 bg-gray-50 py-6 text-center text-sm text-gray-500">
            <div className="flex flex-wrap justify-center gap-4 mb-2">
                <a href="/terms" className="hover:text-gray-700 hover:underline">
                    Terms and Conditions
                </a>
                <a href="/privacy-policy" className="hover:text-gray-700 hover:underline">
                    Privacy Policy
                </a>
            </div>
            <p>&copy; {new Date().getFullYear()} Ministry of Health Trinidad and Tobago. All rights reserved.</p>
        </footer>
    );
}
