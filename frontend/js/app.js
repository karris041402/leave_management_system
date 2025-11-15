// Frontend logic for Employee Leave Management System

const API_BASE_URL = 'http://localhost:8081/api'; // Placeholder, will be exposed in Phase 8
let authToken = null;
let currentUser = null;
let allUsers = [];
let leaveTypes = [];
let leaveRecords = {}; // { 'YYYY-MM': { day: leaveTypeId, ... } }

// --- Utility Functions ---

/**
 * Simple wrapper for fetch API.
 * @param {string} url
 * @param {object} options
 * @returns {Promise<object>}
 */
async function apiFetch(url, options = {}) {
    const headers = {
        'Content-Type': 'application/json',
        ...options.headers,
    };

    if (authToken) {
        headers['Authorization'] = `Bearer ${authToken}`;
    }

    const response = await fetch(url, {
        ...options,
        headers,
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error(data.error || data.message || 'API request failed');
    }

    return data;
}

/**
 * Calculates the number of days in a given month and year.
 * @param {string} monthYear 'YYYY-MM'
 * @returns {number}
 */
function getDaysInMonth(monthYear) {
    const [year, month] = monthYear.split('-').map(Number);
    return new Date(year, month, 0).getDate();
}

/**
 * Generates the HTML for the leave table header based on the provided structure.
 * @returns {string} HTML string for the table header.
 */
function generateTableHeaderHTML() {
    return `
        <thead>
            <tr>
                <th rowspan="3" class="bg-gray-200">PERIOD</th>
                <th rowspan="3" class="bg-gray-200">PARTICULARS</th>
                <th colspan="31" rowspan="3" class="bg-gray-200"></th>
                <th colspan="10" class="vl-header">VACATION LEAVE</th>
                <th colspan="5" class="sl-header">SICK LEAVE</th>
                <th rowspan="3" class="bg-gray-200">REMARKS</th>
            </tr>
            <tr>
                <!-- VL Sub-headers -->
                <th rowspan="2" class="vl-header">TOTAL BALANCE VL</th>
                <th rowspan="2" class="vl-header">EARNED</th>
                <th colspan="2" class="vl-header">Absence Undertime W/Pay</th>
                <th colspan="3" class="vl-header">EQUIVALENT</th>
                <th rowspan="2" class="vl-header">ABSENCE Undertime W/Pay</th>
                <th rowspan="2" class="vl-header">BALANCE</th>
                <th rowspan="2" class="vl-header">ABSENCE Undertime W/o Pay</th>

                <!-- SL Sub-headers -->
                <th rowspan="2" class="sl-header">TOTAL BALANCE SL</th>
                <th rowspan="2" class="sl-header">EARNED</th>
                <th rowspan="2" class="sl-header">ABSENCE Undertime W/Pay</th>
                <th rowspan="2" class="sl-header">BALANCE</th>
                <th rowspan="2" class="sl-header">ABSENCE Undertime W/o Pay</th>
            </tr>
            <tr>
                <!-- VL Sub-sub-headers -->
                <th class="vl-header text-xs">H</th>
                <th class="vl-header text-xs">M</th>
                <th class="vl-header text-xs">Hr</th>
                <th class="vl-header text-xs">Min</th>
                <th class="vl-header text-xs">Total</th>
            </tr>
        </thead>
    `;
}

/**
 * Generates the HTML for a single row of the leave table.
 * @param {string} monthYear 'YYYY-MM'
 * @param {object} records { day: leaveTypeId, ... }
 * @returns {string} HTML string for the table body row.
 */

function generateTableBodyRowHTML(monthYear, records = {}, summary = {}) {
    const daysInMonth = getDaysInMonth(monthYear);
    const [year, month] = monthYear.split('-').map(Number);
    const monthName = new Date(year, month - 1).toLocaleString('default', { month: 'long', year: 'numeric' });

    // Get the day of week for the 1st day of the month
    const firstDay = new Date(year, month - 1, 1).getDay(); // 0 = Sunday, 1 = Monday, etc.

    // Generate days of week based on the first day
    const daysOfWeek = [];
    const dayNames = ['SU', 'M', 'T', 'W', 'TH', 'F', 'SA'];
    for (let i = 0; i < 31; i++) {
        const dayIndex = (firstDay + i) % 7;
        daysOfWeek.push(dayNames[dayIndex]);
    }

    const leaveTypeOptions = leaveTypes.map(type =>
        `<option value="${type.id}" data-code="${type.code}" data-points="${type.point_value}">${type.code}</option>`
    ).join('');

    const dayCells = Array.from({ length: 31 }, (_, i) => {
        const day = i + 1;
        const isDayInMonth = day <= daysInMonth;
        const selectedLeaveTypeId = records[day] || '';

        let content = '';
        let classes = 'day-cell';

        if (isDayInMonth) {
            content = `<select class="day-dropdown" data-day="${day}" data-month-year="${monthYear}">
                <option value="">-</option>
                ${leaveTypeOptions}
            </select>`;
        } else {
            classes += ' bg-gray-100';
        }

        return `<td class="${classes}" data-day="${day}" data-month-year="${monthYear}">${content}</td>`;
    }).join('');



    // Format period as MM/DD-DD/YYYY
    const periodStart = `${String(month).padStart(2, '0')}/01-${String(daysInMonth).padStart(2, '0')}/${year}`;

    // Format particular date as M/D/YYYY (last day of month)
    const particularDate = `${month}/${daysInMonth}/${year}`;

    return `
        <tbody data-month-year="${monthYear}">
            <!-- Month Name Row -->
            <tr>
                <th></th>
                <th></th>
                <th colspan="31" class="font-bold">${monthName}</th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th></th>
            </tr>

            <!-- Days of Week Row -->
            <tr>
                <th></th>
                <th></th>
                ${daysOfWeek.map(day => `<th class="text-xs">${day}</th>`).join('')}
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th></th>
            </tr>

            <!-- Date Numbers Row -->
            <tr>
                <th></th>
                <th></th>
                ${Array.from({ length: daysInMonth }, (_, i) => `<th class="text-xs">${i + 1}</th>`).join('')}
                ${Array.from({ length: 31 - daysInMonth }, () => `<th></th>`).join('')}
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="vl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th class="sl-header"></th>
                <th></th>
            </tr>

            <!-- Data Row (with dropdowns) -->
            <tr>
                <td>${periodStart}</td>
                <td></td>
                ${dayCells}
                <!-- Placeholder for VL/SL totals and balances -->
                <td class="vl-total">0.000</td>
                <td class="vl-header"></td>

                <td class="vl-abs-w-pay-m">
                    <input type="number" class="vl-auto-input input-vl-hours" min="0" value="" />
                </td>

                <td class="vl-abs-w-pay-hr">
                    <input type="number" class="vl-auto-input input-vl-minutes" min="0" value="" />
                </td>

                <td class="vl-equiv-hr">0.000</td>
                <td class="vl-equiv-min">0.000</td>
                <td class="vl-equiv-total">0.000</td>
                <td class="vl-abs-w-pay-total">0.000</td>
                <td class="vl-header"></td>
                <td class="vl-abs-wo-pay">0.000</td>
                <td class="sl-total">0.000</td>
                <td class="sl-earned">0.000</td>
                <td class="sl-abs-w-pay">0.000</td>
                <td class="sl-balance"></td>
                <td class="sl-abs-wo-pay">0.000</td>
                <td></td> <!-- Remarks -->
            </tr>

            <!-- Extra Row (with particular date highlighted) -->
            <tr>
                <td></td>
                <td class="bg-yellow-200">${particularDate}</td>
                ${Array.from({ length: 31 }, () => `<td></td>`).join('')}
                <td class="vl-header"></td>
                <td class="vl-earned">1.25</td>
                <td class="vl-header"></td>
                <td class="vl-header"></td>
                <td class="vl-header"></td>
                <td class="vl-header"></td>
                <td class="vl-header"></td>
                <td class="vl-header"></td>
                <td class="vl-balance"></td>
                <td class="vl-header"></td>
                <td class="sl-header"></td>
                <td class="sl-header"></td>
                <td class="sl-header"></td>
                <td class="sl-header"></td>
                <td class="sl-header"></td>
                <td></td>
            </tr>
        </tbody>
    `;
}

/**
 * Renders the main leave table.
 */
function renderLeaveTable() {
    const container = document.getElementById('leave-table-container');
    if (!container) return;

    let tableHTML = `<table class="leave-table">
        ${generateTableHeaderHTML()}
    `;

    // Append all recorded months
    const sortedMonths = Object.keys(leaveRecords).sort();
    for (const monthYear of sortedMonths) {
        const records = leaveRecords[monthYear].records;
        const summary = leaveRecords[monthYear].summary;
        tableHTML += generateTableBodyRowHTML(monthYear, records, summary);
    }

    tableHTML += `</table>`;
    container.innerHTML = tableHTML;

    // Attach event listeners to new dropdowns
    document.querySelectorAll('.day-dropdown').forEach(dropdown => {
        dropdown.addEventListener('change', handleLeaveChange);

        // Set initial value
        const monthYear = dropdown.dataset.monthYear;
        const day = dropdown.dataset.day;
        const record = leaveRecords[monthYear]?.records;

        if (record && record[day]) {
            dropdown.value = record[day];
        }
    });

    // Attach input listeners for VL Hours/Minutes computation
    document.querySelectorAll(".input-vl-hours, .input-vl-minutes").forEach(input => {
        input.addEventListener("input", (e) => {
            const row = e.target.closest("tr");
            computeVLEquivalent(row);
        });
    });


    // Initial calculation
    calculateAllTotals();
}

/**
 * Handles change event on a leave dropdown.
 * @param {Event} event
 */
function handleLeaveChange(event) {
    const dropdown = event.target;
    const monthYear = dropdown.dataset.monthYear;
    const day = dropdown.dataset.day;
    const leaveTypeId = dropdown.value;

    if (!leaveRecords[monthYear]) {
        leaveRecords[monthYear] = { records: {}, summary: {} };
    }

    if (leaveTypeId) {
        leaveRecords[monthYear].records[day] = parseInt(leaveTypeId);
    } else {
        delete leaveRecords[monthYear].records[day];
    }

    calculateAllTotals();
}

/**
 * Performs the calculation of leave points and updates the table.
 */
function calculateAllTotals() {
    const leaveTypeMap = leaveTypes.reduce((acc, type) => {
        acc[type.id] = type;
        return acc;
    }, {});

    for (const monthYear in leaveRecords) {
        const records = leaveRecords[monthYear].records;
        let totalVLPts = 0;
        let totalSLPts = 0;

        for (const day in records) {
            const leaveTypeId = records[day];
            const type = leaveTypeMap[leaveTypeId];

            if (type) {
                const points = parseFloat(type.point_value);
                // Simple logic: Assume VL/SL/VHD/SL8/SL10/VL8/VL10 affect their respective balances
                // This is a simplification; the client's logic is complex and will require a dedicated function
                if (type.code.startsWith('VL') || type.code.startsWith('VHD')) {
                    totalVLPts += points;
                } else if (type.code.startsWith('SL')) {
                    totalSLPts += points;
                }
                // Other types (SPL, CTO, AB) would affect other balances/totals not explicitly defined here
            }
        }

        // Update the row totals (simplification)
        const tbody = document.querySelector(`tbody[data-month-year="${monthYear}"]`);
        if (tbody) {
            tbody.querySelector('.vl-total').textContent = totalVLPts.toFixed(3);
            tbody.querySelector('.sl-total').textContent = totalSLPts.toFixed(3);
            // In a real system, the balance would be calculated based on previous month's balance + earned - used
            // For now, we'll just show the total used points in the 'Total' column
        }
    }
}


function computeVLEquivalent(row) {
    const hours = parseFloat(row.querySelector(".input-vl-hours")?.value || 0);
    const minutes = parseFloat(row.querySelector(".input-vl-minutes")?.value || 0);

    // 1 hour = 0.125 day
    const hourDay = hours * 0.125;

    // 60 minutes = 0.125 day → 1 minute = 0.0020833 day
    const minuteDay = minutes * (0.125 / 60);

    row.querySelector(".vl-equiv-hr").textContent = hourDay.toFixed(3);
    row.querySelector(".vl-equiv-min").textContent = minuteDay.toFixed(3);
    row.querySelector(".vl-equiv-total").textContent = (hourDay + minuteDay).toFixed(3);

    // Equivalent total affects: Absence Undertime W/Pay (Total)
    row.querySelector(".vl-abs-w-pay-total").textContent = (hourDay + minuteDay).toFixed(3);
}


// --- API Interaction Functions ---

async function fetchAllUsers() {
    try {
        allUsers = await apiFetch(`${API_BASE_URL}/users`);
        const select = document.getElementById('employee-select');
        select.innerHTML = '<option value="">Select Employee</option>';
        allUsers.forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = user.employee_name;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error fetching users:', error);
        alert('Error fetching users: ' + error.message);
    }
}

async function fetchLeaveTypes() {
    try {
        leaveTypes = await apiFetch(`${API_BASE_URL}/leave-types`);
    } catch (error) {
        console.error('Error fetching leave types:', error);
        alert('Error fetching leave types: ' + error.message);
    }
}

async function fetchMonthlyRecords(userId, monthYear) {
    try {
        const data = await apiFetch(`${API_BASE_URL}/leaves/user/${userId}/month/${monthYear}`);

        // Transform API records into the frontend format { day: leaveTypeId, ... }
        const records = data.records.reduce((acc, record) => {
            const day = new Date(record.leave_date).getDate();
            const leaveType = leaveTypes.find(t => t.code === record.code);
            if (leaveType) {
                acc[day] = leaveType.id;
            }
            return acc;
        }, {});

        leaveRecords[monthYear] = {
            records: records,
            summary: data.summary
        };
        renderLeaveTable();
    } catch (error) {
        console.error(`Error fetching records for ${monthYear}:`, error);
        alert(`Error fetching records for ${monthYear}: ` + error.message);
    }
}

async function saveAllRecords() {
    const userId = document.getElementById('employee-select').value;
    if (!userId) {
        alert('Please select an employee.');
        return;
    }

    try {
        for (const monthYear in leaveRecords) {
            const month = new Date(monthYear).getMonth() + 1;
            const year = new Date(monthYear).getFullYear();
            const records = leaveRecords[monthYear].records;
            const summary = leaveRecords[monthYear].summary;

            // Prepare records for API: [{ date: 'YYYY-MM-DD', leave_type_id: 1 }]
            const apiRecords = Object.entries(records).map(([day, leaveTypeId]) => {
                const date = `${monthYear}-${String(day).padStart(2, '0')}`;
                return { date, leave_type_id: leaveTypeId };
            });

            // Prepare summary (simplified for now)
            const apiSummary = {
                month: month,
                year: year,
                vl_balance: parseFloat(summary.vacation_leave_balance || 0),
                sl_balance: parseFloat(summary.sick_leave_balance || 0),
            };

            const payload = {
                user_id: parseInt(userId),
                records: apiRecords,
                summary: apiSummary
            };

            await apiFetch(`${API_BASE_URL}/leaves/save`, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
        }

        alert('All leave records saved successfully!');
    } catch (error) {
        console.error('Error saving records:', error);
        alert('Error saving records: ' + error.message);
    }
}

// --- Event Handlers ---

function handleRenderMonth() {
    const userId = document.getElementById('employee-select').value;
    const monthYear = document.getElementById('month-select').value;

    if (!userId || !monthYear) {
        alert('Please select an employee and a month.');
        return;
    }

    // Clear existing records and fetch only the selected month
    leaveRecords = {};
    fetchMonthlyRecords(userId, monthYear);
}

function handleAppendMonth() {
    const userId = document.getElementById('employee-select').value;
    const monthYear = document.getElementById('month-select').value;

    if (!userId || !monthYear) {
        alert('Please select an employee and a month.');
        return;
    }

    // Fetch and append the selected month's records
    fetchMonthlyRecords(userId, monthYear);
}

// --- Initialization ---

async function initApp() {
    // 1. Authentication (Simplified for now: assume successful login)
    // In a real app, this would be a login form submission
    try {
        // Use the setup endpoint to seed the database first (only once)
        await apiFetch(`${API_BASE_URL}/setup/seed`, { method: 'POST' });

        // Login with the test admin user
        const loginData = await apiFetch(`${API_BASE_URL}/auth/login`, {
            method: 'POST',
            body: JSON.stringify({ username: 'admin', password: 'password' })
        });

        authToken = loginData.token;
        currentUser = loginData.user;
        console.log('Login successful. Token acquired.');

        // 2. Fetch initial data
        await fetchLeaveTypes();
        await fetchAllUsers();

        // 3. Set up event listeners
        document.getElementById('btn-render-month').addEventListener('click', handleRenderMonth);
        document.getElementById('btn-append-month').addEventListener('click', handleAppendMonth);
        document.getElementById('btn-save-all').addEventListener('click', saveAllRecords);

        // Initial table render (empty)
        renderLeaveTable();

    } catch (error) {
        console.error('Application initialization failed:', error);
        alert('Application initialization failed. Check console for details.');
    }
}

document.addEventListener('DOMContentLoaded', initApp);
