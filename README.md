📝 Notes Application
A simple PHP-based note-taking system with categories and search functionality.
✨ Features

Create Notes - Add notes with title, category, and content
Search & Filter - Find notes by keyword or filter by category
Categories - Organize notes into custom categories
Delete Notes - Remove notes with confirmation
Statistics Dashboard - View total notes and categories
Responsive Design - Mobile-friendly Bootstrap interface
File Storage - Data stored in JSON format

🚀 Demo
Show Image
🛠️ Technologies Used

Backend: PHP 7.4+
Frontend: HTML5, CSS3, Bootstrap 5.3
Storage: JSON file system
Icons: Bootstrap Icons
Architecture: Object-Oriented Programming (OOP) + Functional Programming

📁 Project Structure
notes-app/
├── index.php          # Main application file
├── Note.php           # Note class (OOP)
├── NoteManager.php    # Note management class (OOP)
├── functions.php      # Utility functions (Functional Programming)
├── storage/
│   └── notes.json     # Data storage file
└── README.md          # This file
🔧 Installation & Setup
Prerequisites

PHP 7.4 or higher
Web server (Apache/Nginx) or PHP built-in server
Write permissions for storage/ directory

Steps

Clone the repository
bashgit clone https://github.com/YOUR_USERNAME/notes-app.git
cd notes-app

Create storage directory
bashmkdir -p storage
chmod 755 storage

Start the application
Option A: Using PHP built-in server
bashphp -S localhost:8000
Option B: Using XAMPP/WAMP/MAMP

Place files in htdocs folder
Access via http://localhost/notes-app


Open in browser
http://localhost:8000


💻 Usage
Adding Notes

Fill in the "Add New Note" form on the left side
Enter title, category, and content
Click "Add Note" button

Searching & Filtering

Use the search field to find notes by keyword
Select a category from the dropdown to filter
Choose sorting options (by title or category)
Click "Apply Filters"

Managing Notes

View: All notes are displayed as cards
Delete: Click the delete button with confirmation
Statistics: View total notes and categories in the top dashboard

🏗️ Architecture
Object-Oriented Programming (OOP)

Note class: Represents individual notes with properties and methods
NoteManager class: Handles CRUD operations and file management

Functional Programming

searchNotes(): Search functionality
filterByCategory(): Category filtering
validateNoteData(): Input validation
sortNotes*(): Sorting functions

Data Storage

Notes stored in storage/notes.json
JSON format for easy reading and writing
Automatic file creation and directory setup

📊 Code Examples
Creating a Note
php$note = new Note("Meeting Notes", "Work", "Discussed project timeline");
$manager = new NoteManager();
$manager->addNote($note);
Searching Notes
php$notes = $manager->getAllNotes();
$searchResults = searchNotes($notes, "project");
Filtering by Category
php$workNotes = filterByCategory($notes, "Work");
🎨 UI Features

Bootstrap 5.3 for responsive design
Bootstrap Icons for visual elements
Statistics Dashboard showing key metrics
Hover effects and smooth transitions
Mobile-friendly responsive layout
Form validation with error messages

🔒 Security Features

Input sanitization with htmlspecialchars()
Data validation before saving
CSRF protection through form methods
File permission management

📝 Sample Data
The application comes with sample notes including:

Meeting Notes (Work category)
Shopping List (Personal category)
Book Ideas (Creative category)
Workout Plan (Health category)

🤝 Contributing

Fork the repository
Create your feature branch (git checkout -b feature/AmazingFeature)
Commit your changes (git commit -m 'Add some AmazingFeature')
Push to the branch (git push origin feature/AmazingFeature)
Open a Pull Request

📜 License
This project is open source and available under the MIT License.
👨‍💻 Author
Your Name - @YourGitHub
🔮 Future Enhancements

 User authentication system
 Note editing functionality
 Export notes to PDF/CSV
 Rich text editor
 Note tags system
 Search history
 Dark mode toggle
 API endpoints for mobile apps

📞 Support
If you have any questions or issues, please open an issue on GitHub or contact me directly.

⭐ Star this repository if you found it helpful!