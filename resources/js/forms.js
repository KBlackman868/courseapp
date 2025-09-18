// resources/js/forms.js
export function initAjaxForms() {
    document.querySelectorAll('form.ajax-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const url = form.action;
            const method = form.method;
            
            try {
                const response = await fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    toastr.success(data.message);
                    // Optional: redirect or update UI
                } else {
                    toastr.error(data.message);
                }
            } catch (error) {
                toastr.error('Something went wrong!');
                console.error(error);
            }
        });
    });
}