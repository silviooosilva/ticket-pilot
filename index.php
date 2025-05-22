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
        <input type="text" id="repo" class="form-control" value="CacheerPHP" />
        <button class="btn btn-primary mt-2" id="loadIssuesBtn">Carregar Issues</button>
        <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#issueModal" onclick="openIssueModal()">Nova Issue</button>
    </div>
    <div id="issuesList"></div>
</div>

<!-- Issue Modal -->
<div class="modal fade" id="issueModal" tabindex="-1" aria-labelledby="issueModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="issueForm">
        <div class="modal-header">
          <h5 class="modal-title" id="issueModalLabel">Nova Issue</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="issueNumber" name="issueNumber" />
            <div class="mb-3">
                <label>Título</label>
                <input type="text" class="form-control" id="issueTitle" name="title" required />
            </div>
            <div class="mb-3">
                <label>Descrição</label>
                <textarea class="form-control" id="issueBody" name="body"></textarea>
            </div>
            <div class="mb-3">
                <label>Assignees (separados por vírgula)</label>
                <input type="text" class="form-control" id="issueAssignees" name="assignees" />
            </div>
            <div class="mb-3">
                <label>Labels (separados por vírgula)</label>
                <input type="text" class="form-control" id="issueLabels" name="labels" />
            </div>
            <div class="mb-3">
                <label>Milestone</label>
                <select class="form-control" id="issueMilestone" name="milestone"></select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
const apiUrl = window.location.origin + '/api.php';

console.log(apiUrl);

function showSpinner(show) {
    $('#spinner').css('display', show ? 'block' : 'none');
}

function loadMilestones(selectedId = null) {
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    $.get(apiUrl, { action: 'listMilestones', owner, repo }, function(data) {
        // Tenta converter para array se vier como string JSON
        if (typeof data === 'string') {
            try { data = JSON.parse(data); } catch(e) { data = []; }
        }
        let options = '<option value="">Nenhum</option>';
        if (Array.isArray(data)) {
            data.forEach(m => {
                options += `<option value="${m.number}" ${selectedId == m.number ? 'selected' : ''}>${m.title}</option>`;
            });
        } else if (data && data.error) {
            options += `<option value="">Erro: ${data.error}</option>`;
        }
        $('#issueMilestone').html(options);
    });
}

function loadIssues() {
    showSpinner(true);
    const owner = $('#owner').val();
    const repo = $('#repo').val();

    $.get(apiUrl, { action: 'listIssues', owner, repo }, function(data) {

        console.log(data);

        // Tenta converter para array se vier como string JSON
        if (typeof data === 'string') {
            try { data = JSON.parse(data); } catch(e) { data = []; }
        }
        let html = `<table class="table table-bordered"><thead>
            <tr><th>#</th><th>Título</th><th>Status</th><th>Assignees</th><th>Labels</th><th>Milestone</th><th>Ações</th></tr>
            </thead><tbody>`;
        if (Array.isArray(data)) {
            data.forEach(issue => {
                html += `<tr>
                    <td>${issue.number}</td>
                    <td>${issue.title}</td>
                    <td>${issue.state}</td>
                    <td>${(issue.assignees||[]).map(a=>a.login).join(', ')}</td>
                    <td>${(issue.labels||[]).map(l=>l.name).join(', ')}</td>
                    <td>${issue.milestone ? issue.milestone.title : ''}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editIssue(${issue.number})">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteIssue(${issue.number})">Remover</button>
                        <button class="btn btn-sm btn-secondary" onclick="closeIssue(${issue.number})">Fechar</button>
                    </td>
                </tr>`;
            });
        } else if (data && data.error) {
            html += `<tr><td colspan="7" class="text-danger">Erro: ${data.error}</td></tr>`;
        } else {
            html += `<tr><td colspan="7" class="text-danger">Nenhuma issue encontrada ou erro na resposta.</td></tr>`;
        }
        html += '</tbody></table>';
        $('#issuesList').html(html);
        showSpinner(false);
    });
}

function openIssueModal(issue = null) {
    $('#issueForm')[0].reset();
    $('#issueNumber').val('');
    $('#issueModalLabel').text('Nova Issue');
    loadMilestones();
    if (issue) {
        $('#issueModalLabel').text('Editar Issue');
        $('#issueNumber').val(issue.number);
        $('#issueTitle').val(issue.title);
        $('#issueBody').val(issue.body);
        $('#issueAssignees').val((issue.assignees||[]).map(a=>a.login).join(','));
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
        assignees: $('#issueAssignees').val().split(',').map(s=>s.trim()).filter(Boolean),
        labels: $('#issueLabels').val().split(',').map(s=>s.trim()).filter(Boolean),
        milestone: $('#issueMilestone').val() ? parseInt($('#issueMilestone').val()) : null
    };
    let action = number ? 'updateIssue' : 'createIssue';
    let payload = { action, owner, repo, data, issueNumber: number };
    console.log(payload);
    $.ajax({
        url: apiUrl,
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        success: function() {
            $('#issueModal').modal('hide');
            loadIssues();
            showSpinner(false);
        }
    });
});

function deleteIssue(number) {
    if (!confirm('Tem certeza que deseja remover esta issue?')) return;
    showSpinner(true);
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    $.post(apiUrl, JSON.stringify({ action: 'deleteIssue', owner, repo, issueNumber: number }), function() {
        loadIssues();
        showSpinner(false);
    });
}

function closeIssue(number) {
    showSpinner(true);
    const owner = $('#owner').val();
    const repo = $('#repo').val();
    $.post(apiUrl, JSON.stringify({ action: 'closeIssue', owner, repo, issueNumber: number }), function() {
        loadIssues();
        showSpinner(false);
    });
}

$('#loadIssuesBtn').click(loadIssues);

// Carrega milestones ao abrir modal de issue
$('#issueModal').on('show.bs.modal', function() {
    loadMilestones();
});

// Carrega issues ao abrir a página
$(document).ready(function() {
    loadIssues();
});
</script>
</body>
</html>