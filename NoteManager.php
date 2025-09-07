<?php
include_once "Note.php";


class NoteManager {
    private $filePath;

    public function __construct($filePath = "storage/notes.json") {
        $this->filePath = $filePath;
        
        // Create storage directory if it doesn't exist
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Create file if it doesn't exist
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([]));
        }
    }

    public function getAllNotes() {
        if (!file_exists($this->filePath)) {
            return [];
        }
        
        $jsonData = file_get_contents($this->filePath);
        $notesArray = json_decode($jsonData, true);
        
        return $notesArray ?? [];
    }

    public function saveAllNotes($notes) {
        $jsonData = json_encode($notes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($this->filePath, $jsonData) !== false;
    }

    public function addNote(Note $note) {
        $notes = $this->getAllNotes();
        $notes[] = $note->toArray();
        return $this->saveAllNotes($notes);
    }

    public function deleteNote($index) {
        $notes = $this->getAllNotes();
        
        if (isset($notes[$index])) {
            array_splice($notes, $index, 1);
            return $this->saveAllNotes($notes);
        }
        
        return false;
    }

    public function getNoteCount() {
        return count($this->getAllNotes());
    }

    public function searchNotes($keyword) {
        $notes = $this->getAllNotes();
        
        if (empty($keyword)) {
            return $notes;
        }
        
        return array_filter($notes, function($note) use ($keyword) {
            $searchText = strtolower($note['title'] . ' ' . $note['content'] . ' ' . $note['category']);
            return strpos($searchText, strtolower($keyword)) !== false;
        });
    }

    public function filterByCategory($category) {
        $notes = $this->getAllNotes();
        
        if (empty($category)) {
            return $notes;
        }
        
        return array_filter($notes, function($note) use ($category) {
            return strtolower($note['category']) === strtolower($category);
        });
    }

    public function getUniqueCategories() {
        $notes = $this->getAllNotes();
        $categories = array_column($notes, 'category');
        return array_unique($categories);
    }
}
?>