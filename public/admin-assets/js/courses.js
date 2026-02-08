/**
 * Admin Course Management
 */

$(document).ready(function () {
    loadCourses();
});

async function loadCourses() {
    const tableBody = $('#coursesTableBody');
    tableBody.html('<tr><td colspan="7" class="text-center">Loading courses...</td></tr>');

    try {
        const response = await fetch(API_BASE_URL + '/api/courses');
        const data = await response.json();

        if (data.status === 'success') {
            renderCourses(data.data.courses);
        } else {
            tableBody.html(`<tr><td colspan="7" class="text-center text-red">Error: ${data.message}</td></tr>`);
        }
    } catch (error) {
        console.error('Error fetching courses:', error);
        tableBody.html(`<tr><td colspan="7" class="text-center text-red">Network Error: ${error.message}</td></tr>`);
    }
}

function renderCourses(courses) {
    const tableBody = $('#coursesTableBody');
    tableBody.empty();

    if (!courses || courses.length === 0) {
        tableBody.html('<tr><td colspan="7" class="text-center">No courses found</td></tr>');
        return;
    }

    courses.forEach(course => {
        const statusBadge = getStatusBadge(course.statut);
        const priceDisplay = course.prix ? `$${course.prix}` : 'Free';
        const dateDisplay = course.date_creation ? new Date(course.date_creation).toLocaleDateString() : 'N/A';

        const row = `
            <tr>
                <td>#${course.id}</td>
                <td><strong>${course.titre}</strong></td>
                <td>${course.id_professeur}</td>
                <td>${priceDisplay}</td>
                <td>${statusBadge}</td>
                <td>${dateDisplay}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewCourse(${course.id})" title="View"><i class="ti-eye"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCourse(${course.id})" title="Delete"><i class="ti-trash"></i></button>
                </td>
            </tr>
        `;
        tableBody.append(row);
    });
}

function getStatusBadge(status) {
    switch (status) {
        case 'actif': return '<span class="badge badge-success">Active</span>';
        case 'archive': return '<span class="badge badge-dark">Archived</span>';
        case 'inactif': return '<span class="badge badge-warning">Inactive</span>';
        default: return `<span class="badge badge-secondary">${status}</span>`;
    }
}

async function deleteCourse(id) {
    if (!confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(API_BASE_URL + `/api/courses/${id}`, {
            method: 'DELETE'
        });
        const data = await response.json();

        if (data.status === 'success') {
            alert('Course deleted successfully');
            loadCourses();
        } else {
            alert('Error deleting course: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to delete course');
    }
}

function viewCourse(id) {
    // Redirect to course details page (future implementation)
    // window.location.href = `course-details.html?id=${id}`;
    alert('View course details for ID: ' + id + ' (Not implemented yet)');
}
