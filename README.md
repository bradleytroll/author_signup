

```markdown
# Author Signup for Research Projects

**Author Signup for Research Projects** is a PHP-based website designed to allow students to sign up for research projects by selecting an author from a curated list. For each author, the site displays detailed information including a biography, the types of literature they are known for (genre), common themes in their work, and a famous quote from the author. The instructions and warnings are provided in English, Spanish, and Brazilian Portuguese to ensure accessibility for a diverse group of students.

## Features

- **Student Signup Form:**  
  Students provide their first name, last name, and class period, and select an author from a dropdown menu.

- **Dynamic Author Details:**  
  When an author is selected, the page dynamically displays the author’s:
  - **Biography**
  - **Genre** (the types of literature they are known for)
  - **Themes** (common themes in their work)
  - **Quote from the Author** (a famous excerpt or quote)

- **Unique Signups:**  
  Once an author is selected by a student, that author is removed from the available list so that no other student can choose them.

- **Multi-Language Support:**  
  Instructions and warning messages are provided in English, Español, and Português Brasileiro.

- **Submission Logging:**  
  All student signups are logged in a `submissions.log` file for the teacher’s reference.

- **Data Source:**  
  The authors’ information is stored in a JSON file (`authors.json`) that contains 150 author entries, each with name, bio, genre, themes, and a quote from the author.

## Project Structure

```
my-project/
├── index.php             # Main PHP file handling the form and dynamic display
├── authors.json          # JSON data file containing the author information (150 entries)
├── submissions.log       # Log file (created/updated at runtime) to record signups
├── README.md             # This file
└── .gitignore            # Git ignore file (excludes runtime files like submissions.log)
```

## Getting Started

### Prerequisites

- **PHP:**  
  Ensure you have PHP installed on your computer. You can check by running:
  ```bash
  php -v
  ```

- **Web Server:**  
  Run this project using PHP’s built-in server or any other PHP-enabled web server (e.g., Apache, Nginx, XAMPP, MAMP).

### Running the Site Locally

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/yourusername/your-repo.git
   cd your-repo
   ```

2. **Start PHP’s Built-in Server:**
   ```bash
   php -S localhost:8000
   ```
   This command starts a local server on port 8000.

3. **Open Your Browser:**  
   Navigate to [http://localhost:8000/index.php](http://localhost:8000/index.php) to view the site.

## How It Works

- **Form Submission:**  
  When a student submits the form:
  - Their first and last name, class period, and chosen author are recorded.
  - The chosen author is removed from the JSON file, preventing duplicate selections.
  - The submission is logged in the `submissions.log` file.

- **Dynamic Details Display:**  
  The site uses JavaScript to dynamically retrieve and display the selected author’s details (biography, genre, themes, and quote) from the JSON data.

- **Multi-Language Instructions & Warning:**  
  The page displays instructions and warnings in:
  - **English**
  - **Español**
  - **Português Brasileiro**

## Customization

- **Authors Data:**  
  Update the `authors.json` file to modify or add author information. Each entry should follow the structure:
  ```json
  {
    "id": "unique-id",
    "name": "Author Name",
    "bio": "Short biographical description.",
    "genre": "Literary genre(s)",
    "themes": "Theme1, Theme2, ...",
    "sample": "Quote from the Author"
  }
  ```

- **Styling:**  
  Modify the CSS in `index.php` to change the site’s appearance.

- **Functionality:**  
  Expand the PHP and JavaScript code for additional features if needed.

## Contributing

Contributions to improve this project are welcome! If you have suggestions or improvements, please fork the repository and submit a pull request. For major changes, open an issue first to discuss your ideas.

## License

This project is licensed under the [MIT License](LICENSE).

## Acknowledgments

- Special thanks to educators and students who provided valuable feedback.
- The author data (including quotes and descriptions) is based on publicly available information and well-known literary sources.
- This project was inspired by the need for a simple, user-friendly interface for student research signups.

```

---

### How to Download

1. **Copy the above text.**
2. **Create a new file** using your text editor.
3. **Paste the content** into the new file.
4. **Save the file** as `README.md` in your project’s root directory.

This file is now ready to be used in your project. Enjoy your Author Signup website!