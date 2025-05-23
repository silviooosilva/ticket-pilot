<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Pilot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { padding: 2rem; }
        .pointer { cursor: pointer; }
        .modal-lg { max-width: 900px; }
        .spinner { display: none; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">Ticket Pilot</h1>
    <div class="mb-3">
        <label>Owner:</label>
        <input type="text" id="owner" class="form-control" value="silviooosilva" />
        <label>Repo:</label>
        <select id="repo" class="form-control"></select>
        <button class="btn btn-primary mt-2" id="loadIssuesBtn">Load Issues</button>
        <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#issueModal" onclick="openIssueModal()">New Issue</button>
        <button class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#milestoneModal" onclick="openMilestoneModal()">New Milestone</button>
    </div>
    <div id="issuesList"></div>
    <div id="milestonesList" class="mt-4"></div>
</div>

<!-- Issue Modal -->
<div class="modal fade" id="issueModal" tabindex="-1" aria-labelledby="issueModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="issueForm">
        <div class="modal-header">
          <h5 class="modal-title" id="issueModalLabel">New Issue</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="issueNumber" name="issueNumber" />
            <div class="mb-3">
                <label>Title</label>
                <input type="text" class="form-control" id="issueTitle" name="title" required />
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea class="form-control" id="issueBody" name="body"></textarea>
            </div>
            <div class="mb-3">
                <label>Assignees</label>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="assigneesDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        Select assignees
                    </button>
                    <ul class="dropdown-menu w-100" id="assigneesDropdown" aria-labelledby="assigneesDropdownBtn" style="max-height:200px;overflow:auto;">
                        <!-- Items filled in via JS -->
                    </ul>
                </div>
                <input type="hidden" id="issueAssignees" name="assignees" />
            </div>
            <div class="mb-3">
                <label>Labels (separated by comma)</label>
                <input type="text" class="form-control" id="issueLabels" name="labels" />
            </div>
            <div class="mb-3">
                <label>Milestone</label>
                <select class="form-control" id="issueMilestone" name="milestone"></select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeModalFix('issueModal')">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Milestone Modal -->
<div class="modal fade" id="milestoneModal" tabindex="-1" aria-labelledby="milestoneModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="milestoneForm">
        <div class="modal-header">
          <h5 class="modal-title" id="milestoneModalLabel">New Milestone</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="milestoneNumber" name="milestoneNumber" />
            <div class="mb-3">
                <label>Title</label>
                <input type="text" class="form-control" id="milestoneTitle" name="title" required />
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea class="form-control" id="milestoneDescription" name="description"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeModalFix('milestoneModal')">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Spinner -->
<div class="spinner-border text-primary spinner" id="spinner" role="status" style="position:fixed;top:50%;left:50%;z-index:9999;">
  <span class="visually-hidden">Loading...</span>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
const apiUrl = './api.php';

function showSpinner(show) {
    $('#spinner').css('display', show ? 'block' : 'none');
}

function loadMilestones(selectedId = null) {
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    $.get(apiUrl, { action: 'listMilestones', owner, repo }, function(data) {
        if (typeof data === 'string') { try { data = JSON.parse(data); } catch(e) { data = []; } }
        let options = '<option value="">None</option>';
        if (Array.isArray(data)) {
            data.forEach(m => {
                options += `<option value="${m.number}" ${selectedId == m.number ? 'selected' : ''}>${m.title}</option>`;
            });
        }
        $('#issueMilestone').html(options);
        renderMilestonesList(data);
    });
}

function renderMilestonesList(data) {
    let html = `<h4>Milestones</h4>
    <table class="table table-sm table-bordered"><thead>
    <tr><th>#</th><th>Title</th><th>Description</th><th>Actions</th></tr></thead><tbody>`;
    if (Array.isArray(data) && data.length) {
        data.forEach(m => {
            html += `<tr>
                <td>${m.number}</td>
                <td>${m.title}</td>
                <td>${m.description || ''}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editMilestone(${m.number})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteMilestone(${m.number})">Remove</button>
                    <button class="btn btn-sm btn-secondary" onclick="showMilestoneTickets(${m.number})">Tickets</button>
                </td>
            </tr>`;
        });
    } else {
        html += `<tr><td colspan="4" class="text-center">No milestone found.</td></tr>`;
    }
    html += '</tbody></table>';
    $('#milestonesList').html(html);
}

function openMilestoneModal(milestone = null) {
    $('#milestoneForm')[0].reset();
    $('#milestoneNumber').val('');
    $('#milestoneModalLabel').text('New Milestone');
    if (milestone) {
        $('#milestoneModalLabel').text('Edit Milestone');
        $('#milestoneNumber').val(milestone.number);
        $('#milestoneTitle').val(milestone.title);
        $('#milestoneDescription').val(milestone.description);
    }
    var modal = new bootstrap.Modal(document.getElementById('milestoneModal'));
    modal.show();
}

function editMilestone(number) {
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    showSpinner(true);
    $.get(apiUrl, { action: 'getMilestone', owner, repo, milestoneNumber: number }, function(milestone) {
        openMilestoneModal(milestone);
        showSpinner(false);
    });
}

$('#milestoneForm').submit(function(e) {
    e.preventDefault();
    showSpinner(true);
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    const number = $('#milestoneNumber').val();
    const data = {
        title: $('#milestoneTitle').val(),
        description: $('#milestoneDescription').val()
    };
    let action = number ? 'updateMilestone' : 'createMilestone';
    let payload = { action, owner, repo, data, milestoneNumber: number };
    $.ajax({
        url: apiUrl,
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        success: function() {
            closeModalFix('milestoneModal');
            loadMilestones();
            showSpinner(false);
        }
    });
});

function deleteMilestone(number) {
    if (!confirm('Are you sure you want to remove this milestone?')) return;
    showSpinner(true);
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    $.post(apiUrl, JSON.stringify({ action: 'deleteMilestone', owner, repo, milestoneNumber: number }), function() {
        loadMilestones();
        showSpinner(false);
    });
}

function loadAssignees(selected = []) {
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    $.get(apiUrl, { action: 'getAssignees', owner, repo }, function(data) {
        if (typeof data === 'string') { try { data = JSON.parse(data); } catch(e) { data = []; } }
        let html = '';
        if (Array.isArray(data)) {
            data.forEach(a => {
                const checked = selected.includes(a.login) ? 'checked' : '';
                html += `<li>
                    <label class="dropdown-item">
                        <input type="checkbox" class="assignee-checkbox" value="${a.login}" ${checked}> ${a.login}
                    </label>
                </li>`;
            });
        }
        $('#assigneesDropdown').html(html);
        updateAssigneesInput();
    });
}

function updateAssigneesInput() {
    const selected = [];
    $('#assigneesDropdown input:checked').each(function() {
        selected.push($(this).val());
    });
    $('#issueAssignees').val(selected.join(','));
    let btnText = selected.length ? selected.join(', ') : 'Select assignees';
    $('#assigneesDropdownBtn').text(btnText);
}

$(document).on('change', '.assignee-checkbox', updateAssigneesInput);

function loadIssues() {
    showSpinner(true);
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    $.get(apiUrl, { action: 'listIssues', owner, repo }, function(data) {
        if (typeof data === 'string') { try { data = JSON.parse(data); } catch(e) { data = []; } }
        let html = `<table class="table table-bordered"><thead>
            <tr><th>#</th><th>Title</th><th>Status</th><th>Assignees</th><th>Labels</th><th>Milestone</th><th>Actions</th></tr>
            </thead><tbody>`;
        if (Array.isArray(data) && data.length) {
            data.forEach(issue => {
                html += `<tr id="issue-row-${issue.number}">
                    <td>${issue.number}</td>
                    <td>${issue.title}</td>
                    <td>${issue.state}</td>
                    <td>${(issue.assignees||[]).map(a=>a.login).join(', ')}</td>
                    <td>${(issue.labels||[]).map(l=>l.name).join(', ')}</td>
                    <td>${issue.milestone ? issue.milestone.title : ''}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editIssue(${issue.number})">Edit</button>
                        <button class="btn btn-sm btn-secondary" onclick="closeIssue(${issue.number})">Close</button>
                    </td>
                </tr>`;
            });
        } else {
            html += `<tr class="no-issues-row"><td colspan="7" class="text-center">No issues found.</td></tr>`;
        }
        html += '</tbody></table>';
        $('#issuesList').html(html);
        showSpinner(false);
    });
}

function openIssueModal(issue = null) {
    $('#issueForm')[0].reset();
    $('#issueNumber').val('');
    $('#issueModalLabel').text('New Issue');
    loadMilestones();
    loadAssignees([]);
    if (issue) {
        $('#issueModalLabel').text('Edit Issue');
        $('#issueNumber').val(issue.number);
        $('#issueTitle').val(issue.title);
        $('#issueBody').val(issue.body);
        loadAssignees((issue.assignees||[]).map(a=>a.login));
        $('#issueLabels').val((issue.labels||[]).map(l=>l.name).join(','));
        loadMilestones(issue.milestone ? issue.milestone.number : null);
    }
    var modal = new bootstrap.Modal(document.getElementById('issueModal'));
    modal.show();
}

function editIssue(number) {
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    showSpinner(true);
    $.get(apiUrl, { action: 'getIssue', owner, repo, issueNumber: number }, function(issue) {
        openIssueModal(issue);
        showSpinner(false);
    });
}

$('#issueForm').submit(function(e) {
    e.preventDefault();
    showSpinner(true);
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    const number = $('#issueNumber').val();
    const data = {
        title: $('#issueTitle').val(),
        body: $('#issueBody').val(),
        assignees: $('#issueAssignees').val() ? $('#issueAssignees').val().split(',') : [],
        labels: $('#issueLabels').val().split(',').map(s=>s.trim()).filter(Boolean),
        milestone: $('#issueMilestone').val() ? parseInt($('#issueMilestone').val()) : null
    };
    let action = number ? 'updateIssue' : 'createIssue';
    let payload = { action, owner, repo, data, issueNumber: number };
    $.ajax({
        url: apiUrl,
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        success: function(response) {
            closeModalFix('issueModal');
            if (!number && response && response.number) {
                addIssueRow(response);
            } else {
                loadIssues();
            }
            showSpinner(false);
        }
    });
});

function addIssueRow(issue) {
    let html = `<tr id="issue-row-${issue.number}">
        <td>${issue.number}</td>
        <td>${issue.title}</td>
        <td>${issue.state}</td>
        <td>${(issue.assignees||[]).map(a=>a.login).join(', ')}</td>
        <td>${(issue.labels||[]).map(l=>l.name).join(', ')}</td>
        <td>${issue.milestone ? issue.milestone.title : ''}</td>
        <td>
            <button class="btn btn-sm btn-info" onclick="editIssue(${issue.number})">Edit</button>
            <button class="btn btn-sm btn-secondary" onclick="closeIssue(${issue.number})">Close</button>
        </td>
    </tr>`;
    if ($('#issuesList tbody').length) {
        let $tbody = $('#issuesList tbody');
        let $noIssuesRow = $tbody.find('.no-issues-row');
        if ($noIssuesRow.length) $noIssuesRow.remove();
        $tbody.prepend(html);
    } else {
        loadIssues();
    }
}

function closeIssue(number) {
    showSpinner(true);
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    $.post(apiUrl, JSON.stringify({ action: 'closeIssue', owner, repo, issueNumber: number }), function() {
        $(`#issue-row-${number}`).remove();
        if ($('#issuesList tbody tr').length === 0) {
            $('#issuesList tbody').append('<tr class="no-issues-row"><td colspan="7" class="text-center">No issues found.</td></tr>');
        }
        showSpinner(false);
    });
}

$('#loadIssuesBtn').click(function() {
    loadIssues();
    loadMilestones();
});

$('#issueModal').on('show.bs.modal', function() {
    loadMilestones();
    loadAssignees([]);
});

function loadRepositories() {
    const owner = $('#owner').val();
    $.get(apiUrl, { action: 'listPublicRepositories', owner }, function(data) {
        if (typeof data === 'string') { try { data = JSON.parse(data); } catch(e) { data = []; } }
        let options = '';
        if (Array.isArray(data)) {
            data.forEach(repo => {
                if (repo.owner && repo.owner.login === owner) {
                    options += `<option value="${repo.name}">${repo.name}</option>`;
                }
            });
        }
        $('#repo').html(options);
        const firstRepo = $('#repo option:first').val();
        if (firstRepo) {
            $('#repo').val(firstRepo);
            loadIssues();
            loadMilestones();
        } else {
            $('#issuesList').html('');
            $('#milestonesList').html('');
        }
    });
}

$('#repo').on('change', function() {
    loadIssues();
    loadMilestones();
});

$('#owner').on('change', function() {
    loadRepositories();
});

$(document).ready(function() {
    loadRepositories();
});

function closeModalFix(modalId) {
    var modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
    if (modal) modal.hide();
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');
}

function showMilestoneTickets(milestoneNumber) {
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    showSpinner(true);
    $.get(apiUrl, { action: 'listIssues', owner, repo, milestone: milestoneNumber }, function(data) {
        if (typeof data === 'string') { try { data = JSON.parse(data); } catch(e) { data = []; } }
        let html = `<h4>Tickets for Milestone #${milestoneNumber}</h4>
        <table class="table table-bordered"><thead>
            <tr><th>#</th><th>Title</th><th>Status</th><th>Assignees</th><th>Labels</th><th>Actions</th></tr>
            </thead><tbody>`;
        if (Array.isArray(data) && data.length) {
            data.forEach(issue => {
                html += `<tr id="issue-row-${issue.number}">
                    <td>${issue.number}</td>
                    <td>${issue.title}</td>
                    <td>${issue.state}</td>
                    <td>${(issue.assignees||[]).map(a=>a.login).join(', ')}</td>
                    <td>${(issue.labels||[]).map(l=>l.name).join(', ')}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editIssue(${issue.number})">Edit</button>
                        <button class="btn btn-sm btn-secondary" onclick="closeIssue(${issue.number})">Close</button>
                    </td>
                </tr>`;
            });
        } else {
            html += `<tr class="no-issues-row"><td colspan="6" class="text-center">No tickets found for this milestone.</td></tr>`;
        }
        html += '</tbody></table>';
        $('#issuesList').html(html);
        showSpinner(false);
    });
}
</script>
</body>
</html>