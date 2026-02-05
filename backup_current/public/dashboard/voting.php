<?php
// Voting Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('vote')) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$user = $auth->getCurrentUser();
$app = App::getInstance();
$coopDB = $app->getCoopDB();

// Get active voting sessions
try {
    $stmt = $coopDB->prepare("
        SELECT * FROM rat_sessions 
        WHERE status = 'active' 
        AND start_time <= NOW() 
        AND end_time >= NOW()
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $activeSessions = $stmt->fetchAll();
} catch (Exception $e) {
    $activeSessions = [];
}

// Get user's voting history
try {
    $stmt = $coopDB->prepare("
        SELECT vb.*, rs.title, rs.description 
        FROM vote_ballots vb
        JOIN rat_sessions rs ON vb.session_id = rs.id
        WHERE vb.user_id = ?
        ORDER BY vb.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$user['id']]);
    $votingHistory = $stmt->fetchAll();
} catch (Exception $e) {
    $votingHistory = [];
}
?>

<div class="row">
    <div class="col-12">
        <h2>Voting System</h2>
        <p class="text-muted">Partisipasi dalam voting dan rapat anggota tahunan (RAT)</p>
    </div>
</div>

<!-- Active Voting Sessions -->
<div class="row mb-4">
    <div class="col-12">
        <h4>Sesi Voting Aktif</h4>
    </div>
    <?php if (!empty($activeSessions)): ?>
        <?php foreach ($activeSessions as $session): ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($session['title']); ?></h5>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="card-body">
                    <p class="text-muted"><?php echo htmlspecialchars($session['description']); ?></p>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>
                            <?php echo date('d/m/Y H:i', strtotime($session['start_time'])); ?> - 
                            <?php echo date('d/m/Y H:i', strtotime($session['end_time'])); ?>
                        </small>
                    </div>
                    
                    <?php
                    // Check if user has already voted
                    $stmt = $coopDB->prepare("SELECT id FROM vote_ballots WHERE session_id = ? AND user_id = ?");
                    $stmt->execute([$session['id'], $user['id']]);
                    $hasVoted = $stmt->fetch();
                    ?>
                    
                    <?php if ($hasVoted): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-check-circle me-2"></i>
                            Anda telah melakukan voting pada sesi ini
                        </div>
                        <button class="btn btn-outline-info" onclick="viewResults(<?php echo $session['id']; ?>)">
                            <i class="bi bi-bar-chart me-2"></i>Lihat Hasil
                        </button>
                    <?php else: ?>
                        <button class="btn btn-primary" onclick="openVoting(<?php echo $session['id']; ?>)">
                            <i class="bi bi-vote me-2"></i>Voting Sekarang
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Tidak ada sesi voting yang aktif saat ini
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Voting History -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Riwayat Voting</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($votingHistory)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sesi</th>
                                    <th>Pilihan</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($votingHistory as $vote): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vote['title']); ?></td>
                                    <td>
                                        <?php if ($vote['vote_type'] === 'candidate'): ?>
                                            Kandidat: <?php echo htmlspecialchars($vote['vote_data']); ?>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($vote['vote_data']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($vote['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-success">Tercatat</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Belum ada riwayat voting</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Voting Modal -->
<div class="modal fade" id="votingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Voting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="votingContent">
                    <!-- Voting content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Modal -->
<div class="modal fade" id="resultsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hasil Voting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="resultsContent">
                    <!-- Results will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openVoting(sessionId) {
    fetch(`src/public/api/voting.php?action=session&id=${sessionId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const session = data.session;
            const content = `
                <h6>${session.title}</h6>
                <p class="text-muted">${session.description}</p>
                
                <form id="votingForm">
                    <input type="hidden" name="session_id" value="${session.id}">
                    
                    ${session.voting_type === 'candidate' ? `
                        <div class="mb-3">
                            <label class="form-label">Pilih Kandidat</label>
                            <div class="row">
                                ${session.candidates.map((candidate, index) => `
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="vote" 
                                                           value="${candidate.id}" id="candidate_${index}" required>
                                                    <label class="form-check-label" for="candidate_${index}">
                                                        <strong>${candidate.name}</strong><br>
                                                        <small class="text-muted">${candidate.description || ''}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : `
                        <div class="mb-3">
                            <label class="form-label">Pilih Opsi</label>
                            <div class="list-group">
                                ${session.options.map((option, index) => `
                                    <label class="list-group-item">
                                        <input class="form-check-input me-2" type="radio" name="vote" 
                                               value="${option}" id="option_${index}" required>
                                        ${option}
                                    </label>
                                `).join('')}
                            </div>
                        </div>
                    `}
                    
                    <div class="mb-3">
                        <label class="form-label">Komentar (Opsional)</label>
                        <textarea class="form-control" name="comment" rows="3" 
                                  placeholder="Tambahkan komentar jika diperlukan"></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Voting Anda bersifat rahasia dan tidak dapat diubah setelah disubmit
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-vote me-2"></i>Submit Voting
                    </button>
                </form>
            `;
            
            document.getElementById('votingContent').innerHTML = content;
            
            // Add form submit handler
            document.getElementById('votingForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('src/public/api/voting.php?action=vote', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Voting berhasil disubmit');
                        bootstrap.Modal.getInstance(document.getElementById('votingModal')).hide();
                        location.reload();
                    } else {
                        alert(data.message || 'Voting gagal');
                    }
                })
                .catch(error => {
                    alert('Error submitting vote');
                });
            });
            
            new bootstrap.Modal(document.getElementById('votingModal')).show();
        } else {
            alert(data.message || 'Failed to load voting session');
        }
    })
    .catch(error => {
        alert('Error loading voting session');
    });
}

function viewResults(sessionId) {
    fetch(`src/public/api/voting.php?action=results&id=${sessionId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const results = data.results;
            const content = `
                <h6>${results.session.title}</h6>
                <p class="text-muted">Total Votes: ${results.total_votes}</p>
                
                <div class="mb-3">
                    ${results.voting_type === 'candidate' ? `
                        <div class="row">
                            ${results.candidates.map(candidate => `
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>${candidate.name}</h6>
                                            <div class="progress mb-2">
                                                <div class="progress-bar" style="width: ${candidate.percentage}%"></div>
                                            </div>
                                            <small>${candidate.votes} votes (${candidate.percentage}%)</small>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : `
                        <div class="list-group">
                            ${results.options.map(option => `
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>${option.option}</span>
                                    <span>
                                        <strong>${option.votes}</strong> 
                                        <small>(${option.percentage}%)</small>
                                    </span>
                                </div>
                            `).join('')}
                        </div>
                    `}
                </div>
            `;
            
            document.getElementById('resultsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('resultsModal')).show();
        } else {
            alert(data.message || 'Failed to load results');
        }
    })
    .catch(error => {
        alert('Error loading results');
    });
}
</script>
