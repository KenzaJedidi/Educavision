/**
 * Profile Page Script
 * Check auth, load user data, handle updates
 */

$(document).ready(function () {
    // 1. Check Authentication
    if (!Auth.isLoggedIn()) {
        window.location.href = 'login.html';
        return;
    }

    const currentUser = Auth.getCurrentUser();
    if (!currentUser) {
        Auth.logout(); // Corrupted state
        return;
    }

    // 2. Load User Data
    loadUserProfile(currentUser.id);
    loadUserEnrollments(currentUser.id);

    // 3. Handle Profile Update
    $('#form-edit-profile').on('submit', async function (e) {
        e.preventDefault();
        await updateUserProfile(currentUser.id);
    });

    // 4. Handle Password Change
    $('#form-change-password').on('submit', async function (e) {
        e.preventDefault();
        await changeUserPassword(currentUser.id);
    });
});

async function loadUserProfile(userId) {
    // Fetch fresh data from API
    const user = await UserAPI.getUser(userId);

    if (!user) {
        alert('Failed to load user profile');
        return;
    }

    // Update Header Info
    $('#profile-name').text(`${user.prenom} ${user.nom}`);
    $('#profile-email').text(user.email);

    // Populate Form Fields
    $('#input-name').val(`${user.prenom} ${user.nom}`);
    $('#input-occupation').val(user.occupation || ''); // Assuming occupation field exists in DB or JSON
    $('#input-company').val(user.company || '');
    $('#input-phone').val(user.telephone || '');
    $('#input-address').val(user.adresse || '');
    $('#input-city').val(user.ville || '');
    $('#input-state').val(user.pays || ''); // Mapping country/state
    $('#input-postcode').val(user.code_postal || '');

    // Social Links (assuming stored in a social_links JSON column or similar, otherwise plain fields)
    // For now simplistic mapping if fields exist
    $('#input-linkedin').val(user.linkedin || '');
    $('#input-facebook').val(user.facebook || '');
    $('#input-twitter').val(user.twitter || '');
    $('#input-instagram').val(user.instagram || '');
}

async function loadUserEnrollments(userId) {
    const listContainer = $('#masonry');
    listContainer.html('<li class="col-12 text-center">Loading courses...</li>');

    const enrollments = await EnrollmentAPI.getUserEnrollments(userId);

    listContainer.empty();

    if (enrollments.length === 0) {
        listContainer.html('<li class="col-12 text-center">You have not enrolled in any courses yet.</li>');
        return;
    }

    // Since enrollments API returns enrollment objects, we might need course details
    // Assuming enrollment object has course details joined

    enrollments.forEach(enrollment => {
        // Fallback for missing course data
        const title = enrollment.titre || 'Course Title';
        const category = enrollment.categorie || 'General';
        const image = enrollment.image ? `assets/images/courses/${enrollment.image}` : 'assets/images/courses/pic1.jpg';
        const price = enrollment.prix ? `$${enrollment.prix}` : 'Free';
        const link = `courses-details.html?id=${enrollment.id_cours}`;

        const html = `
            <li class="action-card col-xl-4 col-lg-6 col-md-12 col-sm-6">
                <div class="cours-bx">
                    <div class="action-box">
                        <img src="${image}" alt="${title}">
                        <a href="${link}" class="btn">Read More</a>
                    </div>
                    <div class="info-bx text-center">
                        <h5><a href="${link}">${title}</a></h5>
                        <span>${category}</span>
                    </div>
                    <div class="cours-more-info">
                        <div class="review">
                            <span>Enrolled</span>
                            <ul class="cours-star">
                                <li class="active"><i class="fa fa-star"></i></li>
                                <li class="active"><i class="fa fa-star"></i></li>
                                <li class="active"><i class="fa fa-star"></i></li>
                                <li class="active"><i class="fa fa-star"></i></li>
                                <li class="active"><i class="fa fa-star"></i></li>
                            </ul>
                        </div>
                        <div class="price">
                            <h5>${price}</h5>
                        </div>
                    </div>
                </div>
            </li>
        `;
        listContainer.append(html);
    });
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

        // Update local storage user if name changed
        const currentUser = Auth.getCurrentUser();
        currentUser.prenom = prenom;
        currentUser.nom = nom;
        localStorage.setItem('user', JSON.stringify(currentUser));

        // Reload to refresh display
        loadUserProfile(userId);
    } else {
        alert('Error updating profile: ' + result.message);
    }
}

async function changeUserPassword(userId) {
    const currentPass = $('#input-current-password').val();
    const newPass = $('#input-new-password').val();
    const confirmPass = $('#input-confirm-password').val();

    if (!currentPass || !newPass || !confirmPass) {
        alert('Please fill in all password fields');
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
