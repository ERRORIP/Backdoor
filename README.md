# Stealthy PHP File Manager & Backdoor 🔓🗂️

**Disclaimer:**  
This project is intended for educational and research purposes only. Use it on systems you own or have explicit permission to test. The author is not responsible for any misuse or damage caused by this tool.

---

## Overview

This project is a stealthy PHP file manager and backdoor that provides covert system access and file management functionality through a simple web interface. It is designed to work on a web server and allows a user to perform various file operations like browsing directories, uploading, editing, downloading, renaming, moving, compressing, and deleting files. In addition, it features a built-in terminal emulator and a host information page. A unique lock mechanism is also provided to restrict access.

---
![Screenshot of File Manager](11.jpg)

## Key Features

- **Directory Browsing & File Listing**  
  - Lists files and directories in the current working directory with detailed information such as modification time, file size (or '-' for directories), owner/group, and permissions.
  - Displays file details in a subtle, compact style.

- **File Operations**  
  - **View / Edit:** Open files for viewing or editing directly in the browser using CodeMirror (a lightweight code editor) for syntax highlighting.
  - **Download:** Provides a forced download of files regardless of their MIME type (even HTML files) using binary read operations.
  - **Rename, Move & Delete:** Perform common file management tasks through a contextual dropdown menu.
  - **Compress:** Create ZIP archives of files or directories.

- **Upload Functionality**  
  - Supports file uploads with a target directory selection.  
  - Provides a text input for specifying a custom target directory if not using the default directory.
![Screenshot of File Manager](12.jpg)
- **Terminal Emulator**  
  - Integrated Xterm.js terminal that lets you run basic commands on the server.
  - The terminal output is displayed directly on the web page.

- **Host Information**  
  - Displays comprehensive details about the server environment including OS, PHP version, server IP, disk space, and more.
  - Uses a clean, tabular layout for clear readability.

- **Lock Mechanism**  
  - A built-in “lock” feature that can be toggled on/off.  
  - When enabled, the system requires a password (set via a modal prompt) before any file management page can be accessed.
  - The lock state and password are stored in the browser’s localStorage (for session protection).

---
![Screenshot of File Manager](13.jpg)
## Technical Details

- **User Interface & Layout:**  
  The UI is built using HTML, CSS, and JavaScript. The layout includes a header with navigation links for the main sections (File Manager, Upload, Terminal, Info, and Lock). File items are presented using a flexbox layout with the file name and details on the left and a dropdown menu (three-dot button) on the right.

- **File Operations:**  
  The code uses PHP functions such as `scandir()`, `filesize()`, `filemtime()`, and POSIX functions (when available) to retrieve file metadata. Operations like delete, rename, and move are implemented using standard PHP file functions.  
  For downloading files, the code opens the file in binary mode (`rb`) and uses a loop to read and output data in chunks. Appropriate HTTP headers (including cache control and content type forcing download) are sent to prompt the browser to download the file rather than display it.

- **Terminal & Editor Integration:**  
  - **CodeMirror:** Integrated for file editing, it provides syntax highlighting (or plain text mode as configured) with line numbers.  
  - **Xterm.js:** Provides a web-based terminal emulator that supports basic command execution and output display.

- **Lock Mechanism:**  
  The lock feature uses two modal overlays. One modal is used to set a password (with confirmation) when enabling the lock. The other modal prompts for the password on every page load (or refresh) when the lock is active. JavaScript functions control the display and storage (using `localStorage`) of the lock state and password.

- **Security Considerations:**  
  - The tool is intended for use in controlled environments and for educational purposes only.
  - Storing the lock password in localStorage is not a secure method for production use. This method is used here solely for demonstration.
  - Users should ensure no unintended output (like extra whitespace) is sent before HTTP headers to prevent issues with file downloads.
  - It is recommended to further harden the code and access to this tool before using it on any live system.

---

## Usage

1. **Deploy:**  
   Upload the PHP file to your server in a directory with appropriate permissions.

2. **Access:**  
   Open the script in your web browser. You will see a navigation header with options for file management, uploading, terminal access, host info, and lock control.

3. **Lock:**  
   - Toggle the lock by clicking on the "Lock Off" button to set a password.
   - Once locked, every access to the management pages will require you to enter the correct password.

4. **File Operations:**  
   Use the dropdown menu (three dots) next to each file/folder to perform actions like view, edit, rename, move, compress, or delete.

5. **Terminal & Upload:**  
   Access the terminal for command execution or use the upload page to add files to the server.

---

## Contributing & Disclaimer

Feel free to fork and contribute to this project, but always keep in mind that it is provided for educational purposes only. Do not use it to access systems without explicit permission. Any misuse of this tool is at your own risk.

---
