/**
 * Admin User Profile Script
 * Manages viewing and editing user profiles
 */

$(document).ready(function () {
    // 1. Check Auth (handled by admin-auth.js usually, but good to double check)
    // admin-auth.js is included in the page

    // 2. Determine Target User
    const urlParams = new URLSearchParams(window.location.search);
    const paramId = urlParams.get('id');
    const currentUser = Auth.getCurrentUser();

    let targetUserId = paramId ? paramId : (currentUser ? currentUser.id : null);

    if (!targetUserId) {
        alert('No user specified and not logged in.');
        window.location.href = '../login.html';
        return;
    }

    // 3. Load User Data
    loadUserProfile(targetUserId);

    // 4. Handle Profile Update
    $('#form-edit-profile').on('submit', async function (e) {
        e.preventDefault();
        await updateUserProfile(targetUserId);
    });

    // 5. Handle Password Change
    $('#form-change-password').on('submit', async function (e) {
        e.preventDefault();
        await changeUserPassword(targetUserId);
    });
});

async function loadUserProfile(userId) {
    // Fetch fresh data from API
    const user = await UserAPI.getUser(userId);

    if (!user) {
        alert('Failed to load user profile');
        return;
    }

    // Update Header/Breadcrumb if needed (optional)
    $('.breadcrumb-title').text(`User Profile: ${user.prenom} ${user.nom}`);

    // Populate Form Fields
    $('#input-name').val(`${user.prenom} ${user.nom}`);
    $('#input-occupation').val(user.occupation || '');
    $('#input-company').val(user.company || '');
    $('#input-phone').val(user.telephone || '');
    $('#input-address').val(user.adresse || '');
    $('#input-city').val(user.ville || '');
    $('#input-state').val(user.pays || '');
    $('#input-postcode').val(user.code_postal || '');

    $('#input-linkedin').val(user.linkedin || '');
    $('#input-facebook').val(user.facebook || '');
    $('#input-twitter').val(user.twitter || '');
    $('#input-instagram').val(user.instagram || '');
}

async function updateUserProfile(userId) {
    const submitBtn = $('#form-edit-profile button[type="submit"]');
    const originalText = submitBtn.text();
    submitBtn.prop('disabled', true).text('Saving...');

    // Split name
    const fullName = $('#input-name').val().trim();
    const nameParts = fullName.split(' ');
    const prenom = nameParts[0];
    const nom = nameParts.slice(1).join(' ') || '';

    const userData = {
        prenom: prenom,
        nom: nom,
        occupation: $('#input-occupation').val(),
        company: $('#input-company').val(),
        telephone: $('#input-phone').val(),
        adresse: $('#input-address').val(),
        ville: $('#input-city').val(),
        pays: $('#input-state').val(),
        code_postal: $('#input-postcode').val(),
        linkedin: $('#input-linkedin').val(),
        facebook: $('#input-facebook').val(),
        twitter: $('#input-twitter').val(),
        instagram: $('#input-instagram').val()
    };

    const result = await UserAPI.updateUser(userId, userData);

    submitBtn.prop('disabled', false).text(originalText);

    if (result.status === 'success') {
        alert('Profile updated successfully!');

        // If updating self, update local storage
        const currentUser = Auth.getCurrentUser();
        if (currentUser && currentUser.id == userId) {
            currentUser.prenom = prenom;
            currentUser.nom = nom;
            // Update other fields in local object if needed, but essential is name
            localStorage.setItem('user', JSON.stringify(currentUser));
            DOM.updateUserDisplay(); // Refresh navbar name
        }

        loadUserProfile(userId);
    } else {
        alert('Error updating profile: ' + result.message);
    }
}

async function changeUserPassword(userId) {
    const currentPass = $('#input-current-password').val(); // Might be empty if admin changes other user's password? 
    // Logic: If admin is changing another user's password, maybe they don't need current password?
    // But API requires 'old_password'.
    // If I am admin and changing SOMEONE ELSE'S password, I might need a different API endpoint or logic.
    // However, for now, let's assume standard flow (user changing own password) or admin knows user's password (unlikely).
    // If this is an Admin features, typically Admin can Force Reset password without old password.
    // My API `PUT /api/users/{id}/password` implementation in `UserController.php` likely checks old password.

    // For now, I will assume providing current password is required.

    const newPass = $('#input-new-password').val();
    const confirmPass = $('#input-confirm-password').val();

    if (!newPass || !confirmPass) {
        alert('Please fill in new password fields');
        return;
    }

    if (newPass !== confirmPass) {
        alert('New passwords do not match');
        return;
    }

    if (newPass.length < 6) {
        alert('New password must be at least 6 characters long');
        return;
    }

    const submitBtn = $('#form-change-password button[type="submit"]');
    const originalText = submitBtn.text();
    submitBtn.prop('disabled', true).text('Changing...');

    const result = await UserAPI.changePassword(userId, currentPass, newPass);

    submitBtn.prop('disabled', false).text(originalText);

    if (result.status === 'success') {
        alert('Password changed successfully!');
        $('#form-change-password')[0].reset();
    } else {
        alert('Error changing password: ' + result.message);
    }
}
