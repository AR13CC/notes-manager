<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "Note.php";
require_once "NoteManager.php";
require_once "functions.php";

$manager = new NoteManager();
$message = "";
$messageType = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $title = trim($_POST['title']);
        $category = trim($_POST['category']);
        $content = trim($_POST['content']);
        
        $validation = validateNoteData($title, $category, $content);
        
        if ($validation['valid']) {
            $note = new Note($title, $category, $content);
            if ($manager->addNote($note)) {
                $message = "Note added successfully!";
                $messageType = "success";
            } else {
                $message = "Error adding note. Please try again.";
                $messageType = "danger";
            }
        } else {
            $message = "Validation errors: " . implode(", ", $validation['errors']);
            $messageType = "danger";
        }
        
        header("Location: index.php");
        exit;
    }
}

// Handle deletions
if (isset($_GET['delete'])) {
    $index = (int)$_GET['delete'];
    if ($manager->deleteNote($index)) {
        $message = "Note deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting note.";
        $messageType = "danger";
    }
    header("Location: index.php");
    exit;
}

// Get all notes
$allNotes = $manager->getAllNotes();

// Get search and filter parameters
$keyword = $_GET['search'] ?? "";
$categoryFilter = $_GET['category'] ?? "";
$sortBy = $_GET['sort'] ?? "";

// Apply search and filters
$notes = $allNotes;

if (!empty($keyword)) {
    $notes = searchNotes($notes, $keyword);
}

if (!empty($categoryFilter)) {
    $notes = filterByCategory($notes, $categoryFilter);
}

// Apply sorting
if ($sortBy === 'title') {
    $notes = sortNotesByTitle($notes);
} elseif ($sortBy === 'category') {
    $notes = sortNotesByCategory($notes);
}

// Get unique categories for filter dropdown
$categories = array_unique(array_column($allNotes, 'category'));
sort($categories);

// Get statistics
$stats = getNotesStatistics($allNotes);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📝 Notes Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .note-card {
            transition: transform 0.2s ease-in-out;
        }
        .note-card:hover {
            transform: translateY(-2px);
        }
        .stats-card {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
        }
        .search-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="display-4 text-primary"><i class="bi bi-journal-text"></i> Notes Application</h1>
        <p class="lead text-muted">Organize your thoughts with categories and search</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3><i class="bi bi-file-text"></i> <?= $stats['total_notes'] ?></h3>
                            <p class="mb-0">Total Notes</p>
                        </div>
                        <div class="col-md-3">
                            <h3><i class="bi bi-tags"></i> <?= $stats['total_categories'] ?></h3>
                            <p class="mb-0">Categories</p>
                        </div>
                        <div class="col-md-3">
                            <h3><i class="bi bi-search"></i> <?= count($notes) ?></h3>
                            <p class="mb-0">Filtered Results</p>
                        </div>
                        <div class="col-md-3">
                            <h3><i class="bi bi"></i> <?= $stats['total_notes'] > 0 ? round((count($notes) / $stats['total_notes']) * 100, 1) : 0 ?>%</h3>
                            <p class="mb-0">Match Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Add Note Form -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Note</h4>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-card-heading"></i> Title</label>
                            <input type="text" name="title" class="form-control" maxlength="100" required 
                                   placeholder="Enter note title...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-tag"></i> Category</label>
                            <input type="text" name="category" class="form-control" maxlength="50" required 
                                   placeholder="e.g., Work, Personal, Ideas..." list="categories">
                            <datalist id="categories">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-file-text"></i> Content</label>
                            <textarea name="content" rows="4" class="form-control" required 
                                      placeholder="Write your note content here..."></textarea>
                        </div>
                        <button type="submit" name="add" class="btn btn-primary w-100">
                            <i class="bi bi-plus-lg"></i> Add Note
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="col-md-6">
            <div class="search-section">
                <h4 class="mb-3"><i class="bi bi-funnel"></i> Search & Filter</h4>
                <form method="get">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-search"></i> Search</label>
                            <input type="text" name="search" value="<?= htmlspecialchars($keyword) ?>" 
                                   class="form-control" placeholder="Enter keyword...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-filter"></i> Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" 
                                            <?= $cat == $categoryFilter ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-sort-alpha-down"></i> Sort By</label>
                            <select name="sort" class="form-select">
                                <option value="">Default Order</option>
                                <option value="title" <?= $sortBy == 'title' ? 'selected' : '' ?>>Title</option>
                                <option value="category" <?= $sortBy == 'category' ? 'selected' : '' ?>>Category</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-search"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
                
                <?php if (!empty($keyword) || !empty($categoryFilter)): ?>
                    <div class="mt-2">
                        <a href="index.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Notes List -->
    <div class="row">
        <div class="col-12">
            <h2 class="mb-3">
                <i class="bi bi-list-ul"></i> 
                <?php if (!empty($keyword) || !empty($categoryFilter)): ?>
                    Filtered Notes (<?= count($notes) ?> of <?= $stats['total_notes'] ?>)
                <?php else: ?>
                    All Notes (<?= count($notes) ?>)
                <?php endif; ?>
            </h2>
            
            <?php if (empty($notes)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i>
                    <?php if (!empty($keyword) || !empty($categoryFilter)): ?>
                        No notes found matching your search criteria.
                    <?php else: ?>
                        No notes available. Create your first note above!
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($notes as $index => $note): ?>
                        <?php 
                        // Find the actual index in the original array for deletion
                        $actualIndex = array_search($note, $allNotes);
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card note-card h-100 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 text-truncate" title="<?= htmlspecialchars($note['title']) ?>">
                                        <?= htmlspecialchars($note['title']) ?>
                                    </h5>
                                    <span class="badge bg-primary"><?= htmlspecialchars($note['category']) ?></span>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <?= nl2br(htmlspecialchars(truncateText($note['content'], 150))) ?>
                                    </p>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-tag"></i> <?= htmlspecialchars($note['category']) ?>
                                    </small>
                                    <a href="?delete=<?= $actualIndex ?>" class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this note?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>