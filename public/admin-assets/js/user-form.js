/**
 * Admin User Form Handler
 * Handles creating and editing users
 */

$(document).ready(function () {
    // Handle Create User Form
    $('#createUserForm').on('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const userData = Object.fromEntries(formData.entries());

        // Disable button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Creating...');

        try {
            const response = await fetch(API_BASE_URL + '/api/users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(userData)
            });

            const data = await response.json();

            if (data.status === 'success') {
                alert('User created successfully!');
                window.location.href = 'users-list.html';
            } else {
                alert('Error: ' + data.message);
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to connect to server');
        } finally {
            submitBtn.prop('disabled', false).text(originalText);
        }
    });
});
