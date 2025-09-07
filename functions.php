<?php

/**
 * Search notes by keyword in title, content, or category
 * @param array $notes Array of notes
 * @param string $keyword Search keyword
 * @return array Filtered notes
 */
function searchNotes($notes, $keyword) {
    if (empty($keyword)) {
        return $notes;
    }
    
    $keyword = strtolower(trim($keyword));
    
    return array_filter($notes, function($note) use ($keyword) {
        $searchText = strtolower($note['title'] . ' ' . $note['content'] . ' ' . $note['category']);
        return strpos($searchText, $keyword) !== false;
    });
}

/**
 * Filter notes by specific category
 * @param array $notes Array of notes
 * @param string $category Category to filter by
 * @return array Filtered notes
 */
function filterByCategory($notes, $category) {
    if (empty($category)) {
        return $notes;
    }
    
    return array_filter($notes, function($note) use ($category) {
        return strtolower($note['category']) === strtolower($category);
    });
}

/**
 * Sort notes by title alphabetically
 * @param array $notes Array of notes
 * @param bool $ascending Sort order (true for A-Z, false for Z-A)
 * @return array Sorted notes
 */
function sortNotesByTitle($notes, $ascending = true) {
    usort($notes, function($a, $b) use ($ascending) {
        $result = strcasecmp($a['title'], $b['title']);
        return $ascending ? $result : -$result;
    });
    
    return $notes;
}

/**
 * Sort notes by category
 * @param array $notes Array of notes
 * @param bool $ascending Sort order (true for A-Z, false for Z-A)
 * @return array Sorted notes
 */
function sortNotesByCategory($notes, $ascending = true) {
    usort($notes, function($a, $b) use ($ascending) {
        $result = strcasecmp($a['category'], $b['category']);
        return $ascending ? $result : -$result;
    });
    
    return $notes;
}

/**
 * Validate note data
 * @param string $title Note title
 * @param string $category Note category
 * @param string $content Note content
 * @return array Array with 'valid' boolean and 'errors' array
 */
function validateNoteData($title, $category, $content) {
    $errors = [];
    
    if (empty(trim($title))) {
        $errors[] = "Title is required";
    }
    
    if (empty(trim($category))) {
        $errors[] = "Category is required";
    }
    
    if (empty(trim($content))) {
        $errors[] = "Content is required";
    }
    
    if (strlen($title) > 100) {
        $errors[] = "Title must be less than 100 characters";
    }
    
    if (strlen($category) > 50) {
        $errors[] = "Category must be less than 50 characters";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Sanitize input data
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text to specified length
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to add if truncated
 * @return string Truncated text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length - strlen($suffix)) . $suffix;
}

/**
 * Get statistics about notes
 * @param array $notes Array of notes
 * @return array Statistics
 */
function getNotesStatistics($notes) {
    $totalNotes = count($notes);
    $categories = array_column($notes, 'category');
    $uniqueCategories = array_unique($categories);
    $categoryCount = array_count_values($categories);
    
    return [
        'total_notes' => $totalNotes,
        'total_categories' => count($uniqueCategories),
        'categories' => $uniqueCategories,
        'category_counts' => $categoryCount
    ];
}

/**
 * Export notes to JSON format
 * @param array $notes Array of notes
 * @return string JSON string
 */
function exportNotesToJson($notes) {
    return json_encode($notes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Import notes from JSON string
 * @param string $jsonString JSON string
 * @return array|false Array of notes or false on error
 */
function importNotesFromJson($jsonString) {
    $notes = json_decode($jsonString, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }
    
    // Validate structure
    foreach ($notes as $note) {
        if (!isset($note['title']) || !isset($note['category']) || !isset($note['content'])) {
            return false;
        }
    }
    
    return $notes;
}

?>