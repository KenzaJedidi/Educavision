$(document).ready(function () {
    loadUsers();

    // Search functionality with empty state handling
    $('#userSearch').on('keyup', function () {
        const value = $(this).val().toLowerCase();
        let visibleCount = 0;

        $("#usersTableBody tr").each(function () {
            const matches = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(matches);
            if (matches) visibleCount++;
        });

        // Handle empty search results
        if (visibleCount === 0 && $('#usersTableBody tr').not('.no-results').length > 0) {
            if ($('#usersTableBody .no-results').length === 0) {
                $('#usersTableBody').append('<tr class="no-results"><td colspan="6" class="empty-state"><i class="ti-search"></i>No users found matching your search.</td></tr>');
            }
        } else {
            $('#usersTableBody .no-results').remove();
        }
    });

    // Handle Create User Form
    $('#formCreateUser').on('submit', async function (e) {
        e.preventDefault();

        if (!validateForm(this)) return;

        const btn = $(this).find('button[type="submit"]');
        setLoading(btn, true);

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(API_BASE_URL + '/api/users', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.status === 'success') {
                showToast('Success', 'User created successfully', 'success');
                $('#userCreateModal').modal('hide');
                this.reset();
                await loadUsers();
                highlightRow(result.data.user.id);
            } else {
                showToast('Error', result.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error', 'Failed to create user', 'danger');
        } finally {
            setLoading(btn, false, 'Save User');
        }
    });

    // Handle Edit User Form
    $('#formEditUser').on('submit', async function (e) {
        e.preventDefault();

        if (!validateForm(this)) return;

        const btn = $(this).find('button[type="submit"]');
        const id = $('#edit-user-id').val();
        setLoading(btn, true);

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(API_BASE_URL + `/api/users/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.status === 'success') {
                showToast('Success', 'User updated successfully', 'success');
                $('#userEditModal').modal('hide');
                await loadUsers();
                highlightRow(id);
            } else {
                showToast('Error', result.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error', 'Failed to update user', 'danger');
        } finally {
            setLoading(btn, false, 'Update User');
        }
    });

    // Handle Sorting
    $('.table th').css('cursor', 'pointer').on('click', function () {
        const table = $(this).parents('table').eq(0);
        const rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
        this.asc = !this.asc;
        if (!this.asc) { rows = rows.reverse(); }
        for (let i = 0; i < rows.length; i++) { table.append(rows[i]); }

        // Update sorting icons
        $('.table th i.fa-sort').remove();
        $(this).append(`<i class="fa fa-sort-${this.asc ? 'asc' : 'desc'} ml-1"></i>`);
    });
});

function comparer(index) {
    return function (a, b) {
        const valA = getCellValue(a, index), valB = getCellValue(b, index);
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
    };
}

function getCellValue(row, index) {
    return $(row).children('td').eq(index).text();
}

async function loadUsers() {
    const tableBody = $('#usersTableBody');
    tableBody.html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"></div><br><span class="mt-2 d-block">Refreshing users...</span></td></tr>');

    try {
        const response = await fetch(API_BASE_URL + '/api/users');
        const data = await response.json();

        if (data.status === 'success') {
            renderUsers(data.data.users);
        } else {
            tableBody.html(`<tr><td colspan="6" class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x mb-2"></i><br>Error: ${data.message}</td></tr>`);
        }
    } catch (error) {
        console.error('Error fetching users:', error);
        tableBody.html(`<tr><td colspan="6" class="text-center text-danger"><i class="fa fa-plug fa-2x mb-2"></i><br>Network Error: ${error.message}</td></tr>`);
    }
}

function renderUsers(users) {
    const tableBody = $('#usersTableBody');
    tableBody.empty();

    if (users.length === 0) {
        tableBody.html('<tr><td colspan="6" class="empty-state"><i class="ti-user"></i>No users registered in the system yet.</td></tr>');
        return;
    }

    users.forEach(user => {
        const roleBadge = getRoleBadge(user.role);
        const statusBadge = user.actif == 1
            ? '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Active</span>'
            : '<span class="badge badge-danger"><i class="fa fa-times-circle"></i> Inactive</span>';

        const row = `
            <tr id="user-row-${user.id}">
                <td>#${user.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm mr-2" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #f37319 0%, #4c1864 100%); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 14px;">
                            ${user.prenom.charAt(0)}${user.nom.charAt(0)}
                        </div>
                        <div>
                            <div class="font-weight-600">${user.prenom} ${user.nom}</div>
                            <small class="text-secondary"><i class="fa fa-calendar-o"></i> ${new Date(user.date_inscription).toLocaleDateString()}</small>
                        </div>
                    </div>
                </td>
                <td>${user.email}</td>
                <td>${roleBadge}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info" onclick="viewUser(${user.id})" title="View Details"><i class="fa fa-eye"></i></button>
                        ${user.role !== 'admin' ? `
                        <button class="btn btn-sm btn-primary" onclick="prepareEditUser(${user.id})" title="Edit Profile"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})" title="Delete User"><i class="fa fa-trash"></i></button>
                        <button class="btn btn-sm ${user.actif == 1 ? 'btn-warning' : 'btn-success'}" 
                            onclick="${user.actif == 1 ? 'deactivateUser' : 'activateUser'}(${user.id})" 
                            title="${user.actif == 1 ? 'Disable Account' : 'Re-activate Account'}">
                            <i class="fa fa-${user.actif == 1 ? 'lock' : 'unlock'}"></i>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
        tableBody.append(row);
    });
}

function getRoleBadge(role) {
    switch (role) {
        case 'admin': return '<span class="badge badge-primary"><i class="fa fa-shield"></i> Admin</span>';
        case 'professeur': return '<span class="badge badge-info"><i class="fa fa-graduation-cap"></i> Teacher</span>';
        case 'etudiant': return '<span class="badge badge-secondary"><i class="fa fa-user"></i> Student</span>';
        default: return `<span class="badge badge-dark">${capitalize(role)}</span>`;
    }
}

function highlightRow(id) {
    const row = $(`#user-row-${id}`);
    if (row.length) {
        row.addClass('user-row-new');
        $('html, body').animate({
            scrollTop: row.offset().top - 150
        }, 500);
        setTimeout(() => row.removeClass('user-row-new'), 3000);
    }
}

function validateForm(form) {
    let isValid = true;
    $(form).find('.is-invalid').removeClass('is-invalid');
    $(form).find('.invalid-feedback').remove();

    $(form).find('input[required], select[required]').each(function () {
        if (!$(this).val()) {
            isValid = false;
            $(this).addClass('is-invalid');
            $(this).parent().append('<div class="invalid-feedback">This field is required.</div>');
        }
    });

    const emailInput = $(form).find('input[type="email"]');
    if (emailInput.length && emailInput.val()) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!re.test(emailInput.val())) {
            isValid = false;
            emailInput.addClass('is-invalid');
            emailInput.parent().append('<div class="invalid-feedback">Please enter a valid email address.</div>');
        }
    }

    const password = $(form).find('input[name="mot_de_passe"]');
    if (password.length && password.val() && password.val().length < 6) {
        isValid = false;
        password.addClass('is-invalid');
        password.parent().append('<div class="invalid-feedback">Password must be at least 6 characters.</div>');
    }

    return isValid;
}

// Reuse from previous version
async function prepareEditUser(id) {
    try {
        const response = await fetch(API_BASE_URL + `/api/users/${id}`);
        const result = await response.json();

        if (result.status === 'success') {
            const user = result.data.user;
            $('#edit-user-id').val(user.id);
            $('#edit-prenom').val(user.prenom);
            $('#edit-nom').val(user.nom);
            $('#edit-email').val(user.email);
            $('#edit-role').val(user.role);

            $('#userEditModal').modal('show');
        } else {
            showToast('Error', 'Error: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error', 'Failed to load user info', 'danger');
    }
}

async function deleteUser(id) {
    if (!confirm('WARNING: Are you sure you want to delete this user? This cannot be undone.')) return;

    try {
        const response = await fetch(API_BASE_URL + `/api/users/${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();

        if (result.status === 'success') {
            showToast('Deleted', 'User successfully removed', 'success');
            loadUsers();
        } else {
            showToast('Error', result.message, 'danger');
        }
    } catch (error) {
        showToast('Error', 'Delete operation failed', 'danger');
    }
}

async function activateUser(id) { await changeUserStatus(id, 'activate'); }
async function deactivateUser(id) { await changeUserStatus(id, 'deactivate'); }

async function changeUserStatus(id, action) {
    try {
        const response = await fetch(API_BASE_URL + `/api/users/${id}/${action}`, {
            method: 'PUT'
        });
        const result = await response.json();

        if (result.status === 'success') {
            showToast('Updated', `User ${action}d successfully`, 'info');
            loadUsers();
            highlightRow(id);
        } else {
            showToast('Error', result.message, 'danger');
        }
    } catch (error) {
        showToast('Error', 'Status update failed', 'danger');
    }
}

function setLoading(btn, isLoading, originalText = 'Save') {
    if (isLoading) {
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
    } else {
        btn.prop('disabled', false).text(originalText);
    }
}

function showToast(title, message, type = 'info') {
    const id = 'toast-' + Date.now();
    const icon = type === 'success' ? 'check-circle' : (type === 'danger' ? 'exclamation-circle' : 'info-circle');
    const toastHtml = `
        <div id="${id}" class="toast bg-${type} text-white show" style="min-width: 250px; border: none; margin-bottom: 10px;">
            <div class="toast-header bg-${type} text-white border-0 shadow-sm" style="opacity: 0.9;">
                <i class="fa fa-${icon} mr-2"></i>
                <strong class="mr-auto text-uppercase" style="font-size: 11px; letter-spacing: 1px;">${title}</strong>
                <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close" onclick="$('#${id}').remove()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body" style="background: rgba(255,255,255,0.1); font-size: 13px;">
                ${message}
            </div>
        </div>
    `;
    $('#toast-container').prepend(toastHtml);
    setTimeout(() => $(`#${id}`).fadeOut(500, function () { $(this).remove(); }), 4000);
}

function viewUser(id) { window.location.href = `user-profile.html?id=${id}`; }
function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
