// State Management with LocalStorage
const STORAGE_KEY = 'neon_core_users';

let users = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [
    { id: 101, name: 'Alex Vance', email: 'alex@neoncore.io', gender: 'Male' },
    { id: 102, name: 'Elena Rostova', email: 'elena@neoncore.io', gender: 'Female' }
];

// Initialize application
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('currentYear').textContent = new Date().getFullYear();
    renderUserTable();
    setupSearchFilter();
});

function saveToLocalStorage() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(users));
}

// Navigation Handler
function showView(view, scrollToId = null) {
    document.querySelectorAll('.view-section').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));

    if (view === 'dashboard') {
        document.getElementById('dashboard-view').style.display = 'block';
        document.getElementById('nav-dashboard').classList.add('active');
        if (scrollToId) {
            const target = document.getElementById(scrollToId);
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        }
    } else if (view === 'form') {
        document.getElementById('form-view').style.display = 'block';
        document.getElementById('nav-create').classList.add('active');
    }
}

// Render User Directory
function renderUserTable() {
    const tableBody = document.getElementById('userTableBody');
    const totalCount = users.length;

    document.getElementById('totalUsersCount').textContent = totalCount;
    document.getElementById('recordBadge').textContent = `${totalCount} RECORD${totalCount === 1 ? '' : 'S'}`;

    if (totalCount === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="empty-state">
                    <div class="empty-icon">⌕</div>
                    <strong>No users found</strong>
                    <p>Add your first user to get started.</p>
                    <button onclick="openCreateForm()" class="btn btn-primary">Add First User</button>
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = users.map(user => {
        const initial = user.name.trim().charAt(0).toUpperCase();
        return `
            <tr class="user-row" data-id="${user.id}">
                <td class="id-cell">#${user.id}</td>
                <td>
                    <div class="user-info">
                        <div class="avatar">${escapeHtml(initial)}</div>
                        <span class="user-name">${escapeHtml(user.name)}</span>
                    </div>
                </td>
                <td class="email-cell">${escapeHtml(user.email)}</td>
                <td class="gender-cell">${escapeHtml(user.gender || 'N/A')}</td>
                <td>
                    <div class="action-buttons">
                        <button onclick="openEditForm(${user.id})" class="btn btn-edit">Edit</button>
                        <button onclick="deleteUser(${user.id})" class="btn btn-delete">Delete</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('') + `
        <tr id="noResults" style="display:none;">
            <td colspan="5" class="empty-state">
                No matching users found.
            </td>
        </tr>
    `;
}

// Search Filtering Logic
function setupSearchFilter() {
    const searchInput = document.getElementById('userSearch');
    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const searchText = this.value.toLowerCase().trim();
        const userRows = document.querySelectorAll('.user-row');
        const noResults = document.getElementById('noResults');
        let visibleCount = 0;

        userRows.forEach(row => {
            const matches = row.textContent.toLowerCase().includes(searchText);
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        if (noResults) {
            noResults.style.display = visibleCount === 0 ? '' : 'none';
        }
    });
}

// Form Opening Actions
function openCreateForm() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('alertError').style.display = 'none';

    document.getElementById('breadcrumbAction').textContent = 'Add User';
    document.getElementById('formIcon').textContent = '＋';
    document.getElementById('formEyebrow').textContent = 'NEW DATABASE ENTRY';
    document.getElementById('formTitle').innerHTML = 'Add <span class="gradient-text">New User.</span>';
    document.getElementById('formSubtitle').textContent = 'Register a new account in your user directory.';
    document.getElementById('submitBtn').textContent = 'Save User';
    document.getElementById('formFooterTag').textContent = 'NEW RECORD / CREATE';

    showView('form');
}

function openEditForm(id) {
    const user = users.find(u => u.id === id);
    if (!user) return;

    document.getElementById('alertError').style.display = 'none';
    document.getElementById('userId').value = user.id;
    document.getElementById('name').value = user.name;
    document.getElementById('email').value = user.email;
    document.getElementById('gender').value = user.gender || '';

    document.getElementById('breadcrumbAction').textContent = 'Edit User';
    document.getElementById('formIcon').textContent = '✎';
    document.getElementById('formEyebrow').textContent = 'DATABASE MODIFICATION';
    document.getElementById('formTitle').innerHTML = 'Edit <span class="gradient-text">User.</span>';
    document.getElementById('formSubtitle').textContent = `Update the information for user #${user.id}.`;
    document.getElementById('submitBtn').textContent = 'Save Changes';
    document.getElementById('formFooterTag').textContent = `RECORD #${user.id} / EDIT`;

    showView('form');
}

// Form Submission Handler
function handleFormSubmit(e) {
    e.preventDefault();

    const idInput = document.getElementById('userId').value;
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const gender = document.getElementById('gender').value;
    const alertError = document.getElementById('alertError');

    // Email Validation Regex
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!name || !email || !gender) {
        alertError.textContent = 'Please fill in all fields.';
        alertError.style.display = 'block';
        return;
    }

    if (!emailRegex.test(email)) {
        alertError.textContent = 'Please enter a valid email address.';
        alertError.style.display = 'block';
        return;
    }

    if (idInput) {
        // Update existing user
        const userId = parseInt(idInput, 10);
        const index = users.findIndex(u => u.id === userId);
        if (index !== -1) {
            users[index] = { id: userId, name, email, gender };
        }
    } else {
        // Create new user
        const newId = users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 101;
        users.unshift({ id: newId, name, email, gender });
    }

    saveToLocalStorage();
    renderUserTable();
    showView('dashboard');
}

// Delete Action
function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        users = users.filter(user => user.id !== id);
        saveToLocalStorage();
        renderUserTable();
    }
}

// Utility function to escape HTML special characters
function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}